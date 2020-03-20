<?php


namespace App;

use App\Billing\Interfaces\TicketCodeGenerator;
use Hashids\Hashids;

/**
 * Class HashidsTicketCodeGenerator
 * @package App
 */
class HashidsTicketCodeGenerator implements TicketCodeGenerator
{
    /** @var  */
    private $hashids;

    public function __construct($salt)
    {
        $this->hashids = new Hashids($salt, 6, 'ABCDEFGHIJKLMNOPRSTUVWXYZ');
    }

    /**
     * @return mixed|void
     */
    public function generateFor($ticket)
    {
        return $this->hashids->encode($ticket->id);
    }
}