<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carts = Cart::getContent();

        return response()->json(['data' => $carts], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $check_variant = str_contains($request->sku, '_');

        if ($check_variant) {
            $variant = Variant::where(['sku' => $request->sku])->firstOrFail();
            $data =
                ['variant_id' => $variant->id, 'product_id' => $variant->product->id, 'quantity' => $request->quantity ?? 1,
                'name' => $variant->product->name, 'price' => $variant->price_after_offer, 'details' => $request->details,
                'total_price' => $variant->price * ($request->quantity ?? 1),
            ];
        } else {
            $product = Product::where(['sku' => $request->sku])->firstOrFail();
            $data =
                ['product_id' => $product->id, 'quantity' => $request->quantity ?? 1, 'name' => $product->name,
                'price' => $product->price_after_offer, 'details' => $request->details,
                'total_price' => $product->price * ($request->quantity ?? 1),
            ];
        }

        $cart = Cart::where('sku', $request->sku)->where('customer_id', auth()->id())->first();

        if ($cart) {

            $cart->update(['quantity' => $request->quantity ?? $cart->quantity + 1] + $data);

            return response()->json(['message' => 'Success Update Your Cart'], 200);
        } else {
            Cart::create(['sku' => $request->sku, 'customer_id' => auth()->id()] + $data);

            return response()->json(['message' => 'Success Create Item Cart'], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        $cart->delete();

        return response()->json(['message' => 'Success Delete Item In Cart'], 200);
    }

    public function clear()
    {
        Cart::clear();

        return response()->json(['message' => 'Success Clear Cart'], 200);
    }
}
