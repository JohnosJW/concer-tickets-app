<?php


namespace App;


class Reservation
{
    /** @var */
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

    /**
     * @param $paymentGateway
     * @param $paymentToken
     * @return mixed
     */
    public function complete($paymentGateway, $paymentToken)
    {
        $paymentGateway->charge($this->totalCost(), $paymentToken);

        return Order::forTickets($this->tickets(), $this->email(), $this->totalCost());
    }

    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }
}