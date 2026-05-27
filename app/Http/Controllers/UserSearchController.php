<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $viewer = $request->user();

        $users = User::query()
            ->select(['id', 'username'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('username', 'like', $search.'%');
            })
            ->orderBy('username')
            ->paginate(10)
            ->withQueryString();

        return view('users.search', [
            'search' => $search,
            'users' => $users,
            'followingIds' => $viewer?->following()->pluck('users.id')->all() ?? [],
        ]);
    }
}