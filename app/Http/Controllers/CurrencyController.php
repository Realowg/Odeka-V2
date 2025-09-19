<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function switch(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3'
        ]);

        $code = strtoupper($request->currency);
        session(['currency' => $code]);
        if (auth()->check()) {
            auth()->user()->preferred_currency = $code;
            auth()->user()->save();
        }

        return back();
    }
}


