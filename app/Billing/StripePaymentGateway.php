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

    /**
     * @return string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getValidTestToken()
    {
        return \Stripe\Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '314',
            ],
        ], ['api_key' => config('services.stripe.secret')])->id;
    }

    /**
     * @param $callback
     * @return \Illuminate\Support\Collection
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function newChargesDuring($callback)
    {
        /** @var  $latestCharge */
        $latestCharge = $this->lastCharge();

        $callback($this);

        return $this->newChargesSince($latestCharge)->pluck('amount');
    }

    /**
     * @return mixed
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function lastCharge()
    {
        return  array_first(\Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey]
        )['data']);
    }

    /**
     * @param $charge
     * @return \Illuminate\Support\Collection
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function newChargesSince($charge)
    {
        /** @var  $newCharges */
        $newCharges = \Stripe\Charge::all(
            [
                'ending_before' => $charge->id ?? null,
            ],
            ['api_key' => $this->apiKey]
        )['data'];

        return collect($newCharges);
    }
}
