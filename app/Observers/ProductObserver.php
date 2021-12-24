<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProductObserver
{
    /**
     * Handle the Product "creating" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function creating(Product $product)
    {
        if (request()->hasFile('image')) {
            $name = 'images/products/' . time() . rand(11111, 99999) . '.png';
            Image::make(request()->image)->resize(500, 500)->save(public_path($name));
            $product->image = $name;
        }

    }

    /**
     * Handle the Product "updating" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updating(Product $product)
    {
        if (request()->hasFile('image')) {
            if (File::exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            $name = 'images/products/' . time() . rand(11111, 99999) . '.png';
            Image::make(request()->image)->resize(500, 500)->save(public_path($name));
            $product->image = $name;
        }
    }

    /**
     * Handle the Product "deleting" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleting(Product $product)
    {
        if (File::exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
    }
}
