<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();

        return response()->json(['data' => $setting], 200);
    }

    public function store(SettingRequest $request)
    {
        Setting::first()->update($request->validated());

        return response()->json(['message' => 'Success Updated'], 200);
    }

}
