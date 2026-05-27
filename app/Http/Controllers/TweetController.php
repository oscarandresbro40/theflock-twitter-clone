<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TweetController extends Controller
{
    public function index(Request $request): View
    {
        return view('dashboard', [
            'tweets' => $request->user()
                ->tweets()
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:280'],
        ]);

        $request->user()->tweets()->create($validated);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Tweet posted.');
    }

    public function destroy(Request $request, Tweet $tweet): RedirectResponse
    {
        abort_unless($tweet->user()->is($request->user()), 403);

        $tweet->delete();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Tweet deleted.');
    }
}