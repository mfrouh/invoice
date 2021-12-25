<?php

namespace App\Observers;

use App\Mail\InvoiceMail;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

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
        Mail::to($order->customer->email)->send(new InvoiceMail());
        Cart::clear();
    }

}
