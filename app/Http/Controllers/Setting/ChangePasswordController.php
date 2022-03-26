<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\ChangePasswordRequest;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('setting.change-password');
    }

    public function store(ChangePasswordRequest $request)
    {
        auth()->user()->update(['password' => bcrypt($request->validated()['password'])] + $request->validated());

        return response()->json(['message' => 'Success Changed'], 200);
    }
}
