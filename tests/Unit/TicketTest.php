<?php


namespace Tests\Unit;


use App\Concert;
use App\Facades\TicketCode;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testATicketCanBeReserved()
    {
        $ticket = factory(Ticket::class)->create();

        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    public function testATicketCanBeReleased()
    {
        $ticket = factory(Ticket::class)->states('reserved')->create();

        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();
    }

    /** @test */
    public function testATicketCanBeClaimedForAnOrder()
    {
        /** @var  $order */
        $order = factory(Order::class)->create();

        /** @var  $ticket */
        $ticket = factory(Ticket::class)->create(['code' => null]);

        TicketCode::shouldReceive('generateFor')->with($ticket)->andReturn('TICKETCODE1');

        $this->assertNull($ticket->code);

        $ticket->claimFor($order);

        $this->assertContains($ticket->id, $order->tickets->pluck('id'));
        $this->assertEquals('TICKETCODE1', $ticket->code);
    }
}