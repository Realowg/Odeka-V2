<?php

namespace App\Http\Controllers;

class ChannelController extends Controller
{
    public function index()
    {
        $shows = (array) config('settings.channel_shows_json') ?: [];
        return view('index.channel', compact('shows'));
    }

    public function show($show)
    {
        return view('index.channel', ['shows' => (array) config('settings.channel_shows_json') ?: []]);
    }

    public function episode($show, $episode)
    {
        return view('index.channel', ['shows' => (array) config('settings.channel_shows_json') ?: []]);
    }

    public function latestOshow()
    {
        $url = config('settings.oshow_latest_watch_url');
        if ($url) {
            return redirect()->away($url);
        }
        return redirect('/channel');
    }
}


