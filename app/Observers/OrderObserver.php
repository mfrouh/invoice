<?php

namespace App\Observers;

use App\Models\Cart;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        $order->orderDetails()->createMany(Cart::getContent()->toArray());
        $order->invoice()->create(['url' => '/pdf']);
        Cart::clear();
    }

}
