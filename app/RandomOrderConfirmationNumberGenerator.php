<?php


namespace App;

/**
 * Class RandomOrderConfirmationNumberGenerator
 * @package App
 */
class RandomOrderConfirmationNumberGenerator implements OrderConfirmationNumberGenerator
{
    /**
     * @return mixed|void
     */
    public function generate()
    {
        /** @var  $pool */
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ23456789';

        return substr(str_shuffle(str_repeat($pool, 24)), 0, 24);
    }
}