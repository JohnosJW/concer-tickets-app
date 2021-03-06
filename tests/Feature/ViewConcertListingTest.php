<?php

namespace Tests\Feature;

use App\Concert;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewConcertListingTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });
    }

    /** @test */
    public function testGuestsCannotViewAPromotersConcertList()
    {
        /** @var  $response */
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function testPromotersCanOnlyViewAListOfTheirConcerts()
    {
        /** @var  $user */
        $user = factory(User::class)->create();

        /** @var  $concerts */
        $concerts = factory(Concert::class)->create(['user_id' => $user->id]);

        /** @var  $otherUser */
        $otherUser = factory(User::class)->create();

        /** @var  $otherUsersConcert */
        $otherUsersConcert = factory(Concert::class)->create(['user_id' => $otherUser->id]);

        /** @var  $response */
        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);
        $this->assertTrue($response->data('concerts')->contains($concerts[0]));
        $this->assertTrue($response->data('concerts')->contains($concerts[1]));
        $this->assertTrue($response->data('concerts')->contains($concerts[2]));
        $this->assertFalse($response->data('concerts')->contains($otherUsersConcert));
    }

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
