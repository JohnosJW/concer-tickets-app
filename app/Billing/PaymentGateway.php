<?php


namespace App\Billing;

/**
 * Interface PaymentGateway
 * @package App\Billing
 */
interface PaymentGateway
{
    /**
     * @param $amount
     * @param $token
     * @return mixed
     */
    public function charge($amount, $token);
}