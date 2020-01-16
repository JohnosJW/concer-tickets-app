<?php


namespace Tests\Unit;


use App\Concert;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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
        /** @var  $concert */
        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(3);

        /** @var  $tickets */
        $tickets = $concert->findTickets(3);

        /** @var  $reservation */
        $reservation = new Reservation($tickets);

        $this->assertEquals(3600, $reservation->totalCost());
    }
}