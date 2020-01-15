<?php

namespace App;

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
        return $query->whereNull('order_id');
    }

    public function release()
    {
        $this->update(['order_id' => null]);
    }

    public function concert()
    {
       return $this->belongsTo(Concert::class);
    }

    public function getPriceAttribute()
    {
        return $this->concert->ticket_price;
    }
}
