<?php

namespace Tests\Unit;

use App\Billing\Charge;
use App\Concert;
use App\Order;
use App\Reservation;
use App\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery;
use Tests\TestCase;

/**
 * Class OrderTest
 * @package Tests\Unit
 */
class OrderTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testCreatingAnOrderFromTicketsEmailAndCharge()
    {
        /** @var  $charge */
        $charge = new Charge([
            'amount' => 3600,
            'card_last_four' => '1234'
        ]);

        /** @var  $tickets */
        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        /** @var  $order */
        $order = Order::forTickets($tickets, 'john@example.com', $charge);

        $this->assertEquals('john@example.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(1234, $order->card_last_four);

        $tickets->each->shouldHaveReceived('claimFor', [$order]);
    }

    /** @test */
    public function testRetrievingAnOrderByConfirmationNumber()
    {
        /** @var  $order */
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        /** @var  $foundOrder */
        $foundOrder = Order::findByConfirmationNumber('ORDERCONFIRMATION1234');

        $this->assertEquals($order->id, $foundOrder->id);
    }

    /** @test */
    public function testRetrievingANonexistentOrderByConfirmationNumberThrowsAnException()
    {
        $this->expectException(ModelNotFoundException::class);
        Order::findByConfirmationNumber('NONEXISTENTCONFIRMATIONNUMBER');
    }

    /** @test */
    public function testConvertingToAnArray()
    {
        /** @var  $order */
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 6000,
        ]);

        $order->tickets()->saveMany([
            factory(Ticket::class)->create(['code' => 'TICKETCODE1']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE2']),
            factory(Ticket::class)->create(['code' => 'TICKETCODE3'])
        ]);

        /** @var  $result */
        $result = $order->toArray();

        $this->assertEquals([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 6000,
            'tickets' => [
                ['code' => 'TICKETCODE1'],
                ['code' => 'TICKETCODE2'],
                ['code' => 'TICKETCODE3'],
            ]
        ], $result);
    }
}