<?php


namespace App\Facades;


use App\Billing\Interfaces\TicketCodeGenerator;
use Illuminate\Support\Facades\Facade;

/**
 * Class TicketCode
 * @package App\Facades
 */
class TicketCode extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TicketCodeGenerator::class;
    }

    /**
     * @return string|null
     */
    protected static function getMockAbleClass()
    {
        return static::getFacadeAccessor();
    }
}