<?php

namespace App\Http\Controllers;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use App\Order;
use App\Reservation;
use Illuminate\Http\Request;

/**
 * Class ConcertOrdersController
 * @package App\Http\Controllers
 */
class ConcertOrdersController extends Controller
{
    /** @var PaymentGateway  */
    private $paymentGateway;

    /**
     * ConcertOrdersController constructor.
     * @param PaymentGateway $paymentGateway
     */
    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    /**
     * @param $concertId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($concertId)
    {
        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            // Find some tickets
            /** @var  $tickets */
            $tickets = $concert->findTickets(request('ticket_quantity'));

            /** @var  $reservation */
            $reservation = new Reservation($tickets);

            // Creating the order
            /** @var  $order */
            $order = Order::forTickets($tickets, request('email'), $reservation->totalCost());

            // Charging the customer
            $this->paymentGateway->charge($reservation->totalCost(), request('payment_token'));

            return response()->json($order, 201);
        } catch (PaymentFailedException $e) {
            // Cancel order
            $order->cancel();

            return response()->json([], 422);
        } catch (NotEnoughTicketsException $e) {
            return response()->json([], 422);
        }
    }
}
