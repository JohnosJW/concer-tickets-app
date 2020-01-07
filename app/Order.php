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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
   public function tickets()
   {
       return $this->hasMany(Ticket::class);
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
}
