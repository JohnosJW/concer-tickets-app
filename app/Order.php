<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Order
 * @package App
 */
class Order extends Model
{
    /** @var array  */
   protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
   public function concert()
   {
       return $this->belongsTo(Concert::class);
   }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
   public function tickets()
   {
       return $this->hasMany(Ticket::class);
   }

    /**
     * @return int
     */
   public function ticketQuantity()
   {
       return $this->tickets()->count();
   }

    /**
     * @throws \Exception
     */
   public function cancel()
   {
      foreach ($this->tickets as $ticket) {
          $ticket->release();
      }

      $this->delete();
   }

    /**
     * @return array
     */
   public function toArray()
   {
       return [
           'email' => $this->email,
           'ticket_quantity' => $this->ticketQuantity(),
           'amount' => $this->ticketQuantity() * $this->concert->ticket_price,
       ];
   }
}
