<?php

namespace App\Billing;


use App\Billing\Interfaces\PaymentGateway;
use Stripe\Exception\InvalidRequestException;

/**
 * Class StripePaymentGateway
 * @package App\Billing
 */
class StripePaymentGateway implements PaymentGateway
{
    /** @var string  */
    const TEST_CARD_NUMBER = '4242424242424242';

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
            /** @var  $stripeCharge */
            $stripeCharge = \Stripe\Charge::create([
                'amount' => $amount,
                'source' => $token,
                'currency' => 'usd',
            ], ['api_key' => $this->apiKey]);

            $a =  new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
            ]);

//            dd($a);
        } catch (InvalidRequestException $e) {
            throw new PaymentFailedException();
        }
    }

    /**
     * @return string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        return \Stripe\Token::create([
            'card' => [
                'number' => $cardNumber,
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '314',
            ],
        ], ['api_key' => $this->apiKey])->id;
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

        return $this->newChargesSince($latestCharge)->map( function ($stripeCharge) {
            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4']
            ]);
        });
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
