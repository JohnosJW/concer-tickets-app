<?php


namespace App;

/**
 * Interface OrderConfirmationNumberGenerator
 * @package App
 */
interface OrderConfirmationNumberGenerator
{
    /**
     * @return mixed
     */
    public function generate();
}