<?php


namespace App\Billing;

/**
 * Class FakePaymentGateway
 * @package App\Billing
 */
class FakePaymentGateway implements PaymentGateway
{
    /** @var \Illuminate\Support\Collection  */
    private  $charges;

    /** @var  */
    private $beforeFirstChargeCallball;

    /**
     * FakePaymentGateway constructor.
     */
    public function __construct()
    {
        $this->charges = collect();
    }

    /**
     * @return string
     */
    public function getValidTestToken()
    {
        return "valid-token";
    }

    /**
     * @param $amount
     * @param $token
     * @return mixed|void
     */
    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallball !== null) {
            $callback = $this->beforeFirstChargeCallball;
            $this->beforeFirstChargeCallball = null;
            $callback($this);
        }

        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        $this->charges[] = $amount;
    }

    /**
     * @return mixed
     */
    public function totalCharges()
    {
        return $this->charges->sum();
    }

    /**
     * @param $callback
     */
    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallball = $callback;
    }
}