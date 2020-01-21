<?php


namespace App;


class Reservation
{
    /** @var  */
    private $tickets;

    /**
     * Reservation constructor.
     * @param $tickets
     */
    public function __construct($tickets)
    {
        $this->tickets = $tickets;
    }

    /**
     * @return mixed
     */
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }
}