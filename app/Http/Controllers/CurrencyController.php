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

        // Clear cached rates for immediate accuracy when switching
        cache()->forget('rate_'.config('settings.currency_code').'_'.strtoupper($code));

        return back();
    }
}


