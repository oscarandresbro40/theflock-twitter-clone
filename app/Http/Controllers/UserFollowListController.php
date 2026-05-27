<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserFollowListController extends Controller
{
    public function followers(User $user, Request $request): View
    {
        $viewer = $request->user();

        $users = $user->followers()
            ->select(['users.id', 'users.username'])
            ->orderBy('follows.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('users.follow-list', [
            'listType' => 'followers',
            'listUser' => $user,
            'users' => $users,
            'followingIds' => $viewer?->following()->pluck('users.id')->all() ?? [],
        ]);
    }

    public function following(User $user, Request $request): View
    {
        $viewer = $request->user();

        $users = $user->following()
            ->select(['users.id', 'users.username'])
            ->orderBy('follows.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('users.follow-list', [
            'listType' => 'following',
            'listUser' => $user,
            'users' => $users,
            'followingIds' => $viewer?->following()->pluck('users.id')->all() ?? [],
        ]);
    }
}
