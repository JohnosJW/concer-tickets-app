<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testUserCanViewAConcertListing()
    {
        // Arrange
        // Create a concert
        $concert = Concert::create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity and Lethargy',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call (555) 555-55-55',
            'published_at' => Carbon::parse('-1 week'),
        ]);

        // Act
        // View the concert listing
        $response = $this->get('/concerts/' . $concert->id);
        $response->assertStatus(200);

        // Assert
        // See the concert details
        $response->assertSeeText('The Red Chord');
        $response->assertSeeText('with Animosity and Lethargy');
        $response->assertSeeText('December 13, 2016');
        $response->assertSeeText('32.50');
        $response->assertSeeText('The Mosh Pit');
        $response->assertSeeText('123 Example Lane');
        $response->assertSeeText('Laraville, ON 17916');
        $response->assertSeeText('For tickets, call (555) 555-55-55');
    }

//    public function testUserCannotViewUnpublishedConcertListing()
//    {
//        /** @var  $concert */
//        $concert = factory(Concert::class)->create([
//            'published_at' => null
//        ]);
//
//        $response = $this->get('/concerts/' . $concert->id);
////        $response->assertStatus(404);
//    }
}
