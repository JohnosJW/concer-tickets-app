<?php


namespace App\Facades;


use App\OrderConfirmationNumberGenerator;
use Illuminate\Support\Facades\Facade;

/**
 * Class OrderConfirmationNumber
 * @package App\Facades
 */
class OrderConfirmationNumber extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return OrderConfirmationNumberGenerator::class;
    }
}