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
    /** @test  */
    public function testChargesWithAValidPaymentTokenAreSuccessful()
    {
        // Create a new StripePaymentGateway
        /** @var  $paymentGateway */
        $paymentGateway = new StripePaymentGateway(config('services.stripe.secret'));

        /** @var  $token */
        $token = \Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '314',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;


        // Create a new charge for more some amount using a valid token
        $paymentGateway->charge(2500, $token);

        // Verify that the charge was completed successfully
        /** @var  $lastCharge */
        $lastCharge = \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];

        $this->assertEquals(2500, $lastCharge->amount);
    }
}