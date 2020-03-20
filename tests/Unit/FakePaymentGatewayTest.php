<?php


namespace Tests\Unit;


use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Tests\TestCase;
use Tests\Unit\Billing\PaymentGatewayContractTests;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    /**
     * @return FakePaymentGateway
     */
    protected function getPaymentGateway()
    {
        return new FakePaymentGateway();
    }

    /** @test */
    public function testRunningAHookBeforeTheFirstCharge()
    {
        /** @var  $paymentGateway */
        $paymentGateway = new FakePaymentGateway;

        /** @var  $timeCallbackRun */
        $timeCallbackRun = 0;

        $paymentGateway->beforeFirstCharge( function ($paymentGateway) use (&$timeCallbackRun) {
            /** @var  $callbackRun */
            $timeCallbackRun++;

            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(1, $timeCallbackRun);
        $this->assertEquals(5000, $paymentGateway->totalCharges());
    }
}