<?php

namespace App\Observers;

use App\Mail\InvoiceMail;
use App\Models\Cart;
use App\Models\Order;
use App\Notifications\Customer\CreateOrderNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

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
        Notification::send($order->customer, new CreateOrderNotification($order));
        //TODO: Add Coupon In Order
        //TODO: Generate QrCode For Invoice
        //TODO: Generate Pdf For Invoice
        Cart::clear();
    }

}
