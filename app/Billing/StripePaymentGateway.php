<?php

namespace App\Billing;


use Stripe\Charge;
use Stripe\Exception\InvalidRequestException;

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

    /**
     * @param $amount
     * @param $token
     * @return mixed|void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function charge($amount, $token)
    {
        try {
            Charge::create([
                'amount' => $amount,
                'source' => $token,
                'currency' => 'usd',
            ], ['api_key' => $this->apiKey]);
        } catch (InvalidRequestException $e) {
            throw new PaymentFailedException();
        }
    }
}
