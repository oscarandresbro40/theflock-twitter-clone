<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FollowController extends Controller
{
    public function store(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            throw ValidationException::withMessages([
                'follow' => 'You cannot follow yourself.',
            ]);
        }

        Follow::query()->firstOrCreate([
            'follower_id' => $request->user()->getKey(),
            'followed_id' => $user->getKey(),
        ]);

        return redirect()
            ->back()
            ->with('status', 'User followed.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        Follow::query()
            ->where('follower_id', $request->user()->getKey())
            ->where('followed_id', $user->getKey())
            ->delete();

        return redirect()
            ->back()
            ->with('status', 'User unfollowed.');
    }
}