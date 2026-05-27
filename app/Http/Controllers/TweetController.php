<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TweetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'tweets' => $user
                ->tweets()
                ->withCount('likes')
                ->withExists([
                    'likes as liked_by_user' => fn ($query) => $query->where('user_id', $user->getKey()),
                ])
                ->latest()
                ->get(),
            'users' => User::query()
                ->whereKeyNot($user->getKey())
                ->orderBy('name')
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