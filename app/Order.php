<?php

namespace App;

use App\Facades\OrderConfirmationNumber;
use Illuminate\Database\Eloquent\Model;


/**
 * Class OrderConfirmationNumber
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
   public static function forTickets($tickets, $email, $charge)
   {
       $order = self::create([
           'confirmation_number' => OrderConfirmationNumber::generate(),
           'email' => $email,
           'amount' => $charge->amount(),
           'card_last_four' => $charge->cardLastFour()
       ]);

       $tickets->each->claimFor($order);

       return $order;
   }

    /**
     * @param $confirmationNumber
     * @return mixed
     */
   public static function findByConfirmationNumber($confirmationNumber)
   {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
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
   public function toArray() : array
   {
       return [
           'confirmation_number' => $this->confirmation_number,
           'email' => $this->email,
           'amount' => $this->amount,
           'tickets' => $this->tickets->map( function ($ticket) {
               return [
                 'code' => $ticket->code
               ];
           })->all(),
       ];
   }
}
