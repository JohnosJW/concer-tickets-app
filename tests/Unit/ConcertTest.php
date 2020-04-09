<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

/**
 * Class ConcertTest
 * @package Tests\Unit
 */
class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** test */
    public function testCanGetFormattedDate()
    {
        /**
         * Create a concert with a known date
         *
         * @var  $concert
         */
        $concert = factory(Concert::class)->make([
           'date' => Carbon::parse('2016-12-01 8:00pm')
        ]);

        // Verify the date is formatted as expected
        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** test */
    public function testGetFormattedStartTime()
    {
        /**
         * Create a concert with a known date
         *
         * @var  $concert
         */
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00')
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** test */
    public function testCanGetTicketPriceInDollars()
    {
        /**
         * Create a concert with a known date
         *
         * @var  $concert
         */
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** test */
    public function testConcertsWithAPublishedAtDateArePublished()
    {
        /** @var  $publishedConcertA */
        $publishedConcertA = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);

        /** @var  $publishedConcertB */
        $publishedConcertB = factory(Concert::class)->create(['published_at' => Carbon::parse('-1 week')]);

        /** @var  $unpublishedConcertB */
        $unpublishedConcertB = factory(Concert::class)->create(['published_at' => null]);

        /** @var  $publishedConcerts */
        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcertB));
    }

    /** @test */
    public function testConcertsCanBePublished()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)->create(['published_at' => null]);

        $this->assertFalse($concert->isPublished());

        $concert->publish();

        $this->assertTrue($concert->isPublished());
    }

    /** @test */
    public function testCanAddTickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function testTicketsRemainingDoesNotIncludeTicketsAssociatedWithAnOrder()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)->create();

        $concert->tickets()->saveMany(factory(Ticket::class, 30)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(factory(Ticket::class, 20)->create(['order_id' => null]));

        $this->assertEquals(20, $concert->ticketsRemaining());
    }

    /** @test */
    public function testCannotReserveTicketsThatHaveAlreadyBeenPurchased()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)->create()->addTickets(3);

        /** @var  $order */
        $order = factory(Order::class)->create();

        $order->tickets()->saveMany($concert->tickets->take(2));

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotFoundHttpException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Reserving tickets succeeded even though the tickets were already sold.");
    }

    /** @test */
    public function testCannotReserveTicketsThatHaveAlreadyBeenReserved()
    {
        $concert = factory(Concert::class)->create()->addTickets(3);

        $concert->reserveTickets(2, 'john@example.com');

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotFoundHttpException $e) {
            $this->assertEquals(3, $concert->ticketsRemaining());
            return;
        }

        $this->fail("Reserving tickets succeeded even though the tickets were already reserved.");
    }
}
