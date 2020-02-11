<?php


namespace Tests\Unit\Billing;


use App\Billing\PaymentFailedException;

trait PaymentGatewayContractTests
{
    /**
     * @return mixed
     */
    abstract protected function getPaymentGateway();

    /** @test */
    public function testChargesWithAValidPaymentTokenAreSuccessful()
    {
        // Create a new StripePaymentGateway
        /** @var  $paymentGateway */
        $paymentGateway = $this->getPaymentGateway();

        /** @var  $newCharges */
        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        // Create a new charge for more some amount using a valid token
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertCount(1, $newCharges);
        $this->assertEquals(2500, $newCharges->sum());
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
//        $this->assertEquals([4000, 5000], $newCharges->all());
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