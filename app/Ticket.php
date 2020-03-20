<?php

namespace App;

use App\Facades\TicketCode;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Ticket
 * @package App
 */
class Ticket extends Model
{
    /** @var array  */
    protected $guarded = [];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAvailable($query)
    {
        return $query->whereNull('order_id')->whereNull('reserved_at');
    }

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);
    }

    public function release()
    {
        $this->update(['order_id' => null]);
    }

    /**
     * @param $order
     */
    public function claimFor($order)
    {
        $this->code = TicketCode::generateFor($this);
        $order->tickets()->save($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function concert()
    {
       return $this->belongsTo(Concert::class);
    }

    /**
     * @return mixed
     */
    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
