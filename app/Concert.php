<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Concert
 * @package App
 */
class Concert extends Model
{
    /** @var array  */
    protected $guarded = [];

    /** @var array  */
    protected $dates = ['date'];

    /**
     * @param $query
     * @return mixed
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * @return mixed
     */
    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }

    /**
     * @return mixed
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    /**
     * @return string
     */
    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @param $email
     * @param $ticketQuantity
     * @return Model
     */
    public function orderTickets($email, $ticketQuantity)
    {
        $order = $this->orders()->create([
            'email' => $email
        ]);

        $tickets = $this->tickets()->take($ticketQuantity)->get();

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }

    /**
     * @param $quantity
     */
    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }
    }

    /**
     * @return int
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->whereNull('order_id')->count();
    }
}
