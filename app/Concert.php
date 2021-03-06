<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Class Concert
 * @package App
 */
class Concert extends Model
{
    /** @var array */
    protected $guarded = [];

    /** @var array */
    protected $dates = ['date'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    /**
     * @return bool
     */
    public function isPublished()
    {
        return $this->published_at !== null;
    }

    public function publish()
    {
        $this->update(['published_at' => $this->freshTimestamp()]);
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'tickets');
    }

    /**
     * @param $customerEmail
     * @return bool
     */
    public function hasOrderFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    /**
     * @param $customerEmail
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function ordersFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * @param $quantity
     * @param $email
     * @return Reservation
     */
    public function reserveTickets($quantity, $email): Reservation
    {
        /** @var  $tickets */
        $tickets = $this->findTickets($quantity)->each( function ($ticket) {
            $ticket->reserve();
        });

        return new Reservation($tickets, $email);
    }

    /**
     * @param $quantity
     * @return mixed
     */
    public function findTickets($quantity)
    {
        $tickets = $this->tickets()->available()->take($quantity)->get();

        if ($tickets->count() < $quantity) {
            throw new NotFoundHttpException();
        }

        return $tickets;
    }

    /**
     * @param $quantity
     * @return $this
     */
    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->whereNull('order_id')->count();
    }
}
