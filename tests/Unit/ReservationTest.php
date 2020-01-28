<?php


namespace Tests\Unit;


use App\Billing\FakePaymentGateway;
use App\Concert;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

/**
 * Class ReservationTest
 * @package Tests\Unit
 */
class ReservationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testCalculatingTheTotalCost()
    {
//        /** @var  $concert */
//        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(3);
//
//        /** @var  $tickets */
//        $tickets = $concert->findTickets(3);

        /** @var  $tickets */
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        /** @var  $reservation */
        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    public function testReservedTicketsAreReleasedWhenAReservationIsCancelled()
    {
        /** @var  $tickets */
        $tickets = collect([
            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
            Mockery::mock(Ticket::class)->shouldReceive('release')->once()->getMock(),
        ]);

        /** @var  $reservation */
        $reservation = new Reservation($tickets, 'john@example.com');

        $reservation->cancel();
    }

    /** @test */
    public function testRetrievingTheReservationsTickets()
    {
        /** @var  $tickets */
        $tickets = collect([
            (object) ['price' => 1200],
            (object) ['price' => 1200],
            (object) ['price' => 1200],
        ]);

        /** @var  $reservation */
        $reservation = new Reservation($tickets, 'john@example.com');

        $this->assertEquals($tickets, $reservation->tickets());
    }

    /** @test */
    public function testRetrievingTheCustomersEmail()
    {
        /** @var  $reservation */
        $reservation = new Reservation(collect(), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());
    }

    public function testCompletingAReservation()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)
            ->create(['ticket_price' => 1200]);

        /** @var  $tickets */
        $tickets = factory(Ticket::class, 3)
            ->create(['concert_id' => $concert->id]);

        /** @var  $reservation */
        $reservation = new Reservation($tickets, 'john@example.com');

        /** @var  $paymentGateway */
        $paymentGateway = new FakePaymentGateway();

        /** @var  $order */
        $order = $reservation->complete($paymentGateway, $paymentGateway->getValidTestToken());

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3600, $paymentGateway->totalCharges());
    }
}