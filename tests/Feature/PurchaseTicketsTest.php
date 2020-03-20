<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\Interfaces\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class PurchaseTicketsTest
 * @package Tests\Feature
 */
class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    /**
     * @param $concert
     * @param $params
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function orderTickets($concert, $params)
    {
        /** @var  $savedRequest */
        $savedRequest = $this->app['request'];

        /** @var  $response */
        $response = $this->response = $this->json('POST', "/concerts/{$concert->id}/orders", $params);

        $this->app['request'] = $savedRequest;

        return $response;
    }

    /**
     * @param $response
     * @param $field
     */
    private function assertValidationError($response, $field)
    {
        $response->assertStatus(422);
        //        $this->assertArrayHasKey($field, $this->decodeResponseJson());
        //        $this->assertArrayHasKey('email', $this->decodeResponseJson());

    }

    /** @test */
    public function testCustomerCanPurchaseConcertTickets()
    {
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);

        // Create a concert
        $concert = factory(Concert::class)
            ->states('published')
            ->create(['ticket_price' => 3250])
            ->addTickets(3);

        // Purchase concert tickets
        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        // Assert
        $response->assertStatus(201);

        // Make sure the customer was charged the correct amount
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // Make sure that an order exist for this customer
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertNotNull($concert->ordersFor('john@example.com'));
        $this->assertEquals(3, $concert->ordersFor('john@example.com')->first()->ticketQuantity());
    }

    /** @test */
    public function testEmailIsRequiredToPurchaseTickets()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $response = $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertValidationError($response, 'email');
    }

    /** @test */
    public function testAnOrderIsNotCreatedIfPaymentFails()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)
            ->states('published')
            ->create(['ticket_price' => 3250])
            ->addTickets(3);

        /** @var  $response */
        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token'
        ]);

//        $this->assertValidationError($response, 'email');
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    public function testCannotPurchaseTicketsToAnUnpublishedConcert()
    {
        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    public function testCannotPurchaseMoreTicketsThanRemain()
    {
        $concert = factory(Concert::class)
            ->states('published')
            ->create()
            ->addTickets(50);

        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => 'invalid-payment-token'
        ]);

        $response->assertStatus(404);
        $this->assertFalse($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test  */
    public function testCannotPurchaseTicketsAnotherCustomerIsAlreadyTryingToPurchase()
    {
        /** @var  $concert */
        $concert = factory(Concert::class)
            ->states('published')
            ->create([
                'ticket_price' => 1200
            ])
            ->addTickets(3);

        $this->paymentGateway->beforeFirstCharge( function ($paymentGateway) use ($concert) {
            /** @var  $responseB */
            $this->orderTickets($concert, [
                'email' => 'johnB@example.com',
                'ticket_quantity' => 1,
                'payment_token' => $this->paymentGateway->getValidTestToken(),
            ]);
        });

        $this->assertFalse($concert->hasOrderFor('johnB@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());

        /** @var  $response */
        $response = $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertEquals(3600, $this->paymentGateway->totalCharges());
        $this->assertTrue($concert->hasOrderFor('john@example.com'));
        $this->assertEquals(3, $concert->ordersFor('john@example.com')->first()->ticketQuantity());
    }
}
