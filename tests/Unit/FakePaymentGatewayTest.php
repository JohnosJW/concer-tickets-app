<?php


namespace Tests\Unit;


use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Tests\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function testChargesWithAValidPaymentTokenAreSuccessful()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(2500, $paymentGateway->totalCharges());
    }

//    public function testChargesWithAnInvalidPaymentTokenFail()
//    {
//        try {
//            $paymentGateway = new FakePaymentGateway;
//            $paymentGateway->charge(2500, 'invalid-payment-token');
//        } catch (PaymentFailedException $e) {
//            return;
//        }
//
//        $this->fail();
//    }
}