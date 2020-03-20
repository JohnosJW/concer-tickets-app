<?php


namespace App\Billing;

use App\Billing\Interfaces\PaymentGateway;

/**
 * Class FakePaymentGateway
 * @package App\Billing
 */
class FakePaymentGateway implements PaymentGateway
{
    /** @var string  */
    const TEST_CARD_NUMBER = '4242424242424242';

    /** @var \Illuminate\Support\Collection  */
    private  $charges;

    /** @var  */
    private $tokens;

    /** @var  */
    private $beforeFirstChargeCallback;

    /**
     * FakePaymentGateway constructor.
     */
    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    /**
     * @param string $cardNumber
     * @return mixed|string
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        /** @var  $token */
        $token = 'fake-tok_' . str_random(24);

        $this->tokens[$token] = $cardNumber;

        return $token;
    }

    /**
     * @param $amount
     * @param $token
     * @return mixed|void
     */
    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if (! $this->tokens->has($token)) {
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4)
        ]);
    }

    /**
     * @param $callback
     * @return mixed|void
     */
    public function newChargesDuring($callback)
    {
        /** @var  $chargedFrom */
        $chargedFrom = $this->charges->count();

        $callback($this);

        return $this->charges->slice($chargedFrom)->values();
    }

    /**
     * @return mixed
     */
    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }

    /**
     * @param $callback
     */
    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}