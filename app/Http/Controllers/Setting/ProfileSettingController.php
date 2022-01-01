<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\ProfileSettingRequest;

class ProfileSettingController extends Controller
{
    public function index()
    {
        return view('setting.profile-setting');
    }

    public function store(ProfileSettingRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->json(['message' => 'Success Changed'], 200);
    }
}
