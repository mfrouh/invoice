<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttributeRequest;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        $attributes = AttributeResource::collection(Attribute::with('values')->where('product_id', $request->product_id)->get());

        return response()->json(['data' => $attributes], 200);
    }

    public function store(AttributeRequest $request)
    {
        Attribute::create($request->validated());

        return response()->json(['message' => 'Success Created'], 201);
    }

    public function show(Attribute $attribute)
    {
        return response()->json(['data' => new AttributeResource($attribute)], 200);
    }

    public function update(AttributeRequest $request, Attribute $attribute)
    {
        $attribute->update($request->validated());

        return response()->json(['message' => 'Success Updated'], 200);
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return response()->json(['message' => 'Success Deleted'], 200);
    }
}
