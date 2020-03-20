<?php


namespace Tests\Unit\Billing;


use App\Billing\PaymentFailedException;

/**
 * Trait PaymentGatewayContractTests
 * @package Tests\Unit\Billing
 */
trait PaymentGatewayContractTests
{
    /**
     * @return mixed
     */
    abstract protected function getPaymentGateway();

    /** @test */
    public function testChargesWithAValidPaymentTokenAreSuccessful()
    {
        /** @var  $paymentGateway */
        $paymentGateway = $this->getPaymentGateway();

        /** @var  $newCharges */
        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

//        dd($newCharges);

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->map->amount()->sum());
    }

    /** @test */
    public function testCanGetDetailsAboutASuccessfulCharge()
    {
        /** @var  $paymentGateway */
        $paymentGateway = $this->getPaymentGateway();

        /** @var  $charge */
        $charge = $paymentGateway->charge(2500, $paymentGateway->getValidTestToken($paymentGateway::TEST_CARD_NUMBER));

        $this->assertEquals(substr($paymentGateway::TEST_CARD_NUMBER, -4), $charge->cardLastFour());
        $this->assertEquals(2500, $charge->amount());
    }

    /** @test */
    public function testCanFetchChargesCreatedDuringACallBack()
    {
        /** @var  $paymentGateway */
        $paymentGateway = $this->getPaymentGateway();

        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());

        /** @var  $newCharges */
        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken());
        });

        $this->assertCount(2, $newCharges);
//        $this->assertEquals([5000, 4000], $newCharges->map->amount()->all());
    }

    /** @test  */
    public function testChargesWithAnInvalidPaymentTokenFail()
    {

        /** @var  $paymentGateway */
        $paymentGateway = $this->getPaymentGateway();

        /** @var  $newCharges */
        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {

            try {
                $paymentGateway->charge(2500, 'invalid-payment-token');
            } catch (PaymentFailedException $e) {
                return;
            }

            $this->fail("Charging with an invalid token did not throw a PaymentFailedException");
        });

        $this->assertCount(0, $newCharges);
    }
}