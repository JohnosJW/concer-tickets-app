<?php


namespace Tests\Feature;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class AddConcertTest
 * @package Tests\Feature
 */
class AddConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testPromotersCanViewTheAddConcertForm()
    {
        /** @var  $user */
        $user = factory(User::class)->create();

        /** @var  $response */
        $response = $this->actingAs($user)->get('/backstage/concerts/new');

        $response->assertStatus(200);
    }

    /** @test */
    public function testGuestsCannotViewTheAddConcertForm()
    {
        /** @var  $response */
        $response = $this->get('/backstage/concerts/new');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}