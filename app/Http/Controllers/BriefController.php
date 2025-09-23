<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BriefController extends Controller
{
    public function create()
    {
        return view('index.brief');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:100',
            'objectives' => 'nullable|string',
            'budget' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        \DB::table('brief_submissions')->insert(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        try {
            $to = config('settings.email_admin');
            if ($to) {
                Mail::raw('New brief submission: ' . json_encode($data), function ($m) use ($to) {
                    $m->to($to)->subject('New Brief Submission');
                });
            }
        } catch (\Throwable $e) {}

        return redirect('brief')->with('success_message', 'Thanks! We received your brief.');
    }
}


