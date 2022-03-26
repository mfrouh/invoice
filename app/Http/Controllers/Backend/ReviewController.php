<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = ReviewResource::collection(Review::all());

        return response()->json(['data' => $reviews], 200);
    }
}
