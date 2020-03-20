<?php


namespace App\Billing\Interfaces;

/**
 * Interface OrderConfirmationNumberGenerator
 * @package App\Billing\interfaces
 */
interface OrderConfirmationNumberGenerator
{
    /**
     * @return mixed
     */
    public function generate();
}