<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Tweet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Tweet $tweet): RedirectResponse
    {
        Like::query()->firstOrCreate([
            'user_id' => $request->user()->getKey(),
            'tweet_id' => $tweet->getKey(),
        ]);

        return redirect()
            ->back()
            ->with('status', 'Tweet liked.');
    }

    public function destroy(Request $request, Tweet $tweet): RedirectResponse
    {
        Like::query()
            ->where('user_id', $request->user()->getKey())
            ->where('tweet_id', $tweet->getKey())
            ->delete();

        return redirect()
            ->back()
            ->with('status', 'Tweet unliked.');
    }
}