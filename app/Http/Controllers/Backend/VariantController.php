<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\VariantRequest;
use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class VariantController extends Controller
{

    public function index(Request $request)
    {
        $variants = Variant::where('product_id', $request->product_id)->get();

        return response()->json(['data' => $variants], 200);
    }

    public function store(VariantRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $sku_variant = '';
        foreach ($request->except(['price', 'product_id', 'quantity']) as $key => $value) {
            $sku_variant .= '_' . $value;
            $values[] = $value;
        }

        if ($product->attributes->count() != count($values)) {
            return response()->json(['message' => 'Values Should Be Equal Count Attribute'], 403);
        }
        $sku = 'p' . $product->id . $sku_variant;
        $check_sku_variant = Variant::where('sku', $sku)->first();

        if ($check_sku_variant) {
            return response()->json(['message' => 'This Variant Found'], 403);
        }

        $variant = Variant::create([
            "product_id" => $request->product_id, "sku" => $sku,
            'price' => $request->price, 'quantity' => $request->quantity]);

        $variant->values()->sync($values);
        return response()->json(['message' => 'Success Created'], 201);

    }

    public function update(VariantRequest $request, Variant $variant)
    {
        $variant->update($request->validated());

        return response()->json(['message' => 'Success Updated'], 200);
    }

    public function destroy(Variant $variant)
    {
        $variant->delete();

        return response()->json(['message' => 'Success Deleted'], 200);
    }
}
