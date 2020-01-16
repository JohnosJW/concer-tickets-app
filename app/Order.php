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
     * @param $tickets
     * @param $email
     * @return mixed
     */
   public static function forTickets($tickets, $email, $amount = null)
   {
       $order = self::create([
           'email' => $email,
           'amount' => $amount,
       ]);

       foreach ($tickets as $ticket) {
           $order->tickets()->save($ticket);
       }

       return $order;
   }

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
           'amount' => $this->amount,
       ];
   }
}
