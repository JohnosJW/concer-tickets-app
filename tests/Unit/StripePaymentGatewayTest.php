<?php


namespace Tests\Unit;


use App\Billing\StripePaymentGateway;
use Tests\TestCase;

/**
 * Class StripePaymentGatewayTest
 * @package Tests\Unit
 */
class StripePaymentGatewayTest extends TestCase
{
    /**
     * @return mixed
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function lastCharge()
    {
        return \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];
    }

    /**
     * @return string
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function validToken()
    {
        return \Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '314',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;
    }

    /**
     * @return mixed
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function newCharges()
    {
        return \Stripe\Charge::all(
            [
                'limit' => 1,
                'ending_before' => $this->lastCharge->id,
            ],
            ['api_key' => config('services.stripe.secret')]
        )['data'];
    }

    /**
     * @throws \Stripe\Exception\ApiErrorException
     */
    protected function setUp()
    {
        parent::setUp();

        /** @var  lastCharge */
        $this->lastCharge = $this->lastCharge();
    }

    /** @test  */
    public function testChargesWithAValidPaymentTokenAreSuccessful()
    {
        // Create a new StripePaymentGateway
        /** @var  $paymentGateway */
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        // Create a new charge for more some amount using a valid token
        $paymentGateway->charge(2500, $this->validToken());

        $this->assertCount(1, $this->newCharges());
        $this->assertEquals(2500, $this->lastCharge()->amount);
    }
}