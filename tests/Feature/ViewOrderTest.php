<?php


namespace Tests\Feature;


use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class ViewOrderTest
 * @package Tests\Feature
 */
class ViewOrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test  */
    public function testUserCanViewTheirOrderConfirmation()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)->create();

        /** @var  $order */
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four' => '1881',
            'amount' => 8500
        ]);

        /** @var  $ticket */
        $ticket = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE12'
        ]);

        // Visit the order confirmation page
        $response = $this->get("/orders/ORDERCONFIRMATION1234");

        $response->assertStatus(200);

        // Assert we see the correct order details
        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $order->id == $viewOrder->id;
        });

        // Assets for View
        $response->assertSee('ORDERCONFIRMATION1234');
        $response->assertSee('8500');
        $response->assertSee('1881');
//        $response->assertSee('TICKETCODE12');
    }
}