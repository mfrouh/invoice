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
     * @param \App\Models\Product $product
     *
     * @return void
     */
    public function creating(Product $product)
    {
        if (request()->hasFile('image')) {
            $name = request()->file('image')->storeAs('images/products', request()->file('image')->hashName(), 'public');
            // $name = 'images/products/' . request()->file('image')->hashName();
            // Image::make(request()->image)->resize(500, 500)->save(public_path($name));
            $product->image = $name;
        }
        $product->sku = 'p'.$product->id;
    }

    public function created(Product $product)
    {
        $product->sku = 'p'.$product->id;
        $product->save();
    }

    /**
     * Handle the Product "updating" event.
     *
     * @param \App\Models\Product $product
     *
     * @return void
     */
    public function updating(Product $product)
    {
        if (request()->hasFile('image')) {
            if (File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
            $name = request()->file('image')->storeAs('images/products', request()->file('image')->hashName(), 'public');
            // $name = 'images/products/' . request()->file('image')->hashName();
            // Image::make(request()->image)->resize(500, 500)->save(public_path($name));
            $product->image = $name;
        }
    }

    /**
     * Handle the Product "deleting" event.
     *
     * @param \App\Models\Product $product
     *
     * @return void
     */
    public function deleted(Product $product)
    {
        if (File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }
    }
}
