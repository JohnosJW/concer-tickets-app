<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testCreatingAnOrderFromTicketsEmailAndAmount()
    {
        $concert = factory(Concert::class)->create()->addTickets(5);

        $order = Order::forTickets($concert->findTickets(3), 'john@example.com', 3600);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    public function testConvertingToAnArray()
    {
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(5);

        $order = $concert->orderTickets('john@example.com', 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'john@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000,
        ], $result);
    }

    /** @test */
    public function testTicketsAreReleasedWhenAnOrderIsCanceled()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        $order = $concert->orderTickets('john@example.com', 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());

        $this->assertNull(Order::find($order->id));
    }

    /** @test */
    public function testCreatingAnOrderFromAReservation()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)->create(['ticket_price' => 1200]);

        /** @var  $tickets */
        $tickets = factory(Ticket::class, 3)->create(['concert_id' => $concert->id]);

        /** @var  $reservation */
        $reservation = new Reservation($tickets, 'john@example.com');

        /** @var  $order */
        $order = Order::fromReservation($reservation);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
    }
}