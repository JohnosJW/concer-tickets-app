<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testTicketsAreReleasedWhenAnOrderIsCanceled()
    {
        $concert = factory(Concert::class)->create();

        $concert->addTickets(10);

        $order = $concert->orderTickets('john@example.com', 5);

        $this->assertEquals(5, $concert->ticketsRemaining());

        $order->cancel();

        $this->assertEquals(10, $concert->ticketsRemaining());

        $this->assertNull(Order::find($order->id));
    }
}