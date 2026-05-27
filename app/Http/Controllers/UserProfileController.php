<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class UserProfileController extends Controller
{
    public function show(User $user): View
    {
        return view('users.show', [
            'profileUser' => $user,
            'tweets' => $user->tweets()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }
}
