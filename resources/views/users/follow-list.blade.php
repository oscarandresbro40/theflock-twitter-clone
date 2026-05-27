@php
    $isAuthenticated = auth()->check();
    $isFollowersList = $listType === 'followers';
    $title = $isFollowersList ? __('Followers') : __('Following');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 font-sans antialiased text-gray-900">
        @if ($isAuthenticated)
            @include('layouts.navigation')
        @else
            <header class="border-b border-gray-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</a>

                    <nav class="flex items-center gap-4 text-sm text-gray-600">
                        <a href="{{ route('users.search') }}" class="hover:text-gray-900">{{ __('Search') }}</a>
                        <a href="{{ route('login') }}" class="hover:text-gray-900">{{ __('Log in') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hover:text-gray-900">{{ __('Register') }}</a>
                        @endif
                    </nav>
                </div>
            </header>
        @endif

        <main class="py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <h1 class="text-2xl font-semibold text-gray-900">{{ $listUser->username }} {{ $title }}</h1>

                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-600">
                            <a href="{{ route('users.followers', $listUser) }}" class="hover:text-gray-900 {{ $isFollowersList ? 'font-semibold text-gray-900' : '' }}">
                                {{ __('Followers') }}
                            </a>
                            <a href="{{ route('users.following', $listUser) }}" class="hover:text-gray-900 {{ $isFollowersList ? '' : 'font-semibold text-gray-900' }}">
                                {{ __('Following') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        @if ($users->isEmpty())
                            <p class="text-sm text-gray-600">{{ __('No users found in this list yet.') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach ($users as $user)
                                    <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 p-4">
                                        <div class="space-y-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->username }}</p>
                                            <div class="flex items-center gap-3 text-xs text-gray-600">
                                                <a href="{{ route('users.followers', $user) }}" class="hover:text-gray-900">{{ __('Followers') }}</a>
                                                <a href="{{ route('users.following', $user) }}" class="hover:text-gray-900">{{ __('Following') }}</a>
                                            </div>
                                        </div>

                                        @auth
                                            @if (auth()->id() === $user->id)
                                                <span class="text-sm text-gray-500">{{ __('You') }}</span>
                                            @elseif (in_array($user->id, $followingIds, true))
                                                <form method="POST" action="{{ route('follows.destroy', $user) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <x-secondary-button>{{ __('Unfollow') }}</x-secondary-button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('follows.store', $user) }}">
                                                    @csrf

                                                    <x-primary-button>{{ __('Follow') }}</x-primary-button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                                {{ __('Log in to follow') }}
                                            </a>
                                        @endauth
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
