<?php


namespace App;


class Reservation
{
    /** @var  */
    private $tickets;

    public $email;

    /**
     * Reservation constructor.
     * @param $tickets
     */
    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    /**
     * @return mixed
     */
    public function tickets()
    {
        return $this->tickets;
    }

    /**
     * @return mixed
     */
    public function email()
    {
        return $this->email;
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }
}