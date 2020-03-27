<?php


namespace Tests\Feature;


use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Class PromoteLoginTest
 * @package Tests\Feature
 */
class PromoteLoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testLoggingInWithValidCredentials()
    {
      /** @var  $user */
      $user = factory(User::class)->create([
          'email' => 'jane@example.com',
          'password' => bcrypt('super-secret-password'),
      ]);

      /** @var  $response */
      $response = $this->post('/login', [
          'email' => 'jane@example.com',
          'password' => 'super-secret-password',
      ]);

      $response->assertRedirect('/backstage/concerts');
      $this->assertTrue(Auth::check());
      $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    public function testLoggingInWithInValidCredentials()
    {
        /** @var  $user */
        $user = factory(User::class)->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        /** @var  $response */
        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function testLoggingInWithAnAccountThatDoesNotExist()
    {
        /** @var  $response */
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::check());
    }
}