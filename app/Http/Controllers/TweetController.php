<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TweetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $followedUserIds = $user->following()->select('users.id');

        return view('dashboard', [
            'tweets' => Tweet::query()
                ->with('user:id,username')
                ->where(function (Builder $query) use ($user, $followedUserIds): void {
                    $query
                        ->where('user_id', $user->getKey())
                        ->orWhereIn('user_id', $followedUserIds);
                })
                ->withCount('likes')
                ->withExists([
                    'likes as liked_by_user' => fn ($query) => $query->where('user_id', $user->getKey()),
                ])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(10)
                ->withQueryString(),
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