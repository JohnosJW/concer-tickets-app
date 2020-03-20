<?php


namespace App\Billing\Interfaces;

/**
 * Interface PaymentGateway
 * @package App\Billing\Interfaces
 */
interface PaymentGateway
{
    /**
     * @param $amount
     * @param $token
     * @return mixed
     */
    public function charge($amount, $token);

    /**
     * @return mixed
     */
    public function getValidTestToken();

    /**
     * @param $callback
     * @return mixed
     */
    public function newChargesDuring($callback);
}