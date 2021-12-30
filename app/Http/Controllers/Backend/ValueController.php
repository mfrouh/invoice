<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValueRequest;
use App\Models\Value;
use Illuminate\Http\Request;

class ValueController extends Controller
{

    public function index(Request $request)
    {
        $values = Value::where('attribute_id', $request->attribute_id)->get();

        return response()->json(['data' => $values], 200);
    }

    public function store(ValueRequest $request)
    {
        Value::create($request->validated());

        return response()->json(['message' => 'Success Created'], 201);
    }

    public function show(Value $value)
    {
        return response()->json(['data' => $value], 200);
    }

    public function update(ValueRequest $request, Value $value)
    {
        $value->update($request->validated());

        return response()->json(['message' => 'Success Updated'], 200);
    }

    public function destroy(Value $value)
    {
        $value->variants()->delete();
        $value->delete();
        return response()->json(['message' => 'Success Deleted'], 200);
    }
}
