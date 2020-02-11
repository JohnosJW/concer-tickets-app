<?php


namespace Tests\Unit;


use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Tests\TestCase;
use Tests\Unit\Billing\PaymentGatewayContractTests;

/**
 * Class StripePaymentGatewayTest
 * @package Tests\Unit
 *
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    /**
     * @return StripePaymentGateway
     */
    protected function getPaymentGateway()
    {
       return new StripePaymentGateway(config('services.stripe.secret'));
    }
}