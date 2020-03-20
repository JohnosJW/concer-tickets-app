<?php

namespace App\Http\Controllers;

use App\Order;

/**
 * Class OrdersController
 * @package App\Http\Controllers
 */
class OrdersController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($confirmationNumber)
    {
        /** @var  $order */
        $order = Order::findByConfirmationNumber($confirmationNumber);

        return view('orders.show', ['order' => $order]);
    }
}
