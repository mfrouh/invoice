<?php

namespace App\Http\Controllers\Frontend\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
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
        $product = Product::findOrFail($request->product_id);

        Cart::updateOrCreate(
            ['product_id' => $product->id, 'customer_id' => auth()->id()],
            ['quantity' => $request->quantity ?? 1, 'name' => $product->name,
                'price' => $product->price, 'details' => $request->details,
                'total_price' => $product->price * ($request->quantity ?? 1),
            ]);

        return response()->json(['message' => 'Success Created'], 201);
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

        return response()->json(['message' => 'Success Deleted'], 200);
    }

    public function clear()
    {
        Cart::clear();

        return response()->json(['message' => 'Success Clear'], 200);
    }
}
