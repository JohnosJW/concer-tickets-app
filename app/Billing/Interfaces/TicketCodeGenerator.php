<?php


namespace App\Billing\Interfaces;

/**
 * Interface TicketCodeGenerator
 * @package App\Billing\Interfaces
 */
interface TicketCodeGenerator
{
    /**
     * @return mixed
     */
    public function generateFor($ticket);
}