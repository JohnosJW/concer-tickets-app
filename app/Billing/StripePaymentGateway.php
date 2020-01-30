<?php

namespace App\Billing;


use Stripe\Charge;

/**
 * Class StripePaymentGateway
 * @package App\Billing
 */
class StripePaymentGateway implements PaymentGateway
{
    /** @var  */
    private $apiKey;

    /**
     * StripePaymentGateway constructor.
     * @param $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function charge($amount, $token)
    {
        Charge::create([
           'amount' => $amount,
           'source' => $token,
           'currency' => 'usd',
        ], ['api_key' => $this->apiKey]);
    }
}
