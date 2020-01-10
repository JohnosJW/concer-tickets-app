<?php

namespace Tests\Unit;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    /** test */
    public function testCanOrderConcertTickets()
    {
        $concert = factory(Concert::class)->create()->addTickets(50);

        $order = $concert->orderTickets('john@ex.com', 3);

        $this->assertEquals('john@ex.com', $order->email);
        $this->assertEquals(3, $order->tickets()->count());
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
        $concert = factory(Concert::class)->create()->addTickets(50);

        $concert->orderTickets('john@ex.com', 30);

        $this->assertEquals(20, $concert->ticketsRemaining());
    }
}
