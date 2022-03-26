<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Http\Resources\OfferResource;
use App\Models\Offer;

class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $offers = OfferResource::collection(Offer::all());

        return response()->json(['data' => $offers], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OfferRequest $request)
    {
        Offer::create($request->validated());

        return response()->json(['message' => 'Success Created'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Offer $offer
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Offer $offer)
    {
        return response()->json(['data' => new OfferResource($offer)], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Offer        $offer
     *
     * @return \Illuminate\Http\Response
     */
    public function update(OfferRequest $request, Offer $offer)
    {
        $offer->update($request->validated());

        return response()->json(['message' => 'Success Updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Offer $offer
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();

        return response()->json(['message' => 'Success Deleted'], 200);
    }
}
