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
        @auth
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
        @endauth

        <main class="py-10">
            <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">
                <section class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 shrink-0 rounded-full bg-gray-200 p-2 text-gray-500">
                                <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z" />
                                </svg>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h1 class="text-2xl font-semibold text-gray-900">{{ $profileUser->username }}</h1>
                                <p class="mt-2 text-sm text-gray-700">{{ $profileUser->bio ?: __('No bio yet.') }}</p>

                                <div class="mt-4 flex items-center gap-5 text-sm text-gray-600">
                                    <a href="{{ route('users.followers', $profileUser) }}" class="hover:text-gray-900">
                                        <span class="font-semibold text-gray-900">{{ $profileUser->followers()->count() }} {{ __('followers') }}</span>
                                    </a>
                                    <a href="{{ route('users.following', $profileUser) }}" class="hover:text-gray-900">
                                        <span class="font-semibold text-gray-900">{{ $profileUser->following()->count() }} {{ __('following') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Tweets') }}</h2>

                        @if ($tweets->isEmpty())
                            <p class="mt-4 text-sm text-gray-600">{{ __('This user has not posted any tweets yet.') }}</p>
                        @else
                            <div class="mt-4 space-y-4">
                                @foreach ($tweets as $tweet)
                                    <article class="rounded-lg border border-gray-200 p-4">
                                        <p class="whitespace-pre-wrap text-sm text-gray-900">{{ $tweet->body }}</p>
                                        @if ($tweet->image_path)
                                            <img src="{{ asset('storage/'.$tweet->image_path) }}" alt="{{ __('Tweet image') }}" class="mt-3 max-h-80 w-full rounded-lg border border-gray-200 object-cover">
                                        @endif
                                        <p class="mt-2 text-xs text-gray-500">{{ $tweet->created_at->format('M j, Y g:i A') }}</p>
                                        <p class="mt-2 text-sm text-gray-600">{{ trans_choice('{0} 0 replies|{1} 1 reply|[2,*] :count replies', $tweet->replies_count, ['count' => $tweet->replies_count]) }}</p>
                                        <a href="{{ route('tweets.show', $tweet) }}" class="mt-2 inline-block text-xs font-medium text-gray-700 hover:text-gray-900">{{ __('View thread') }}</a>
                                    </article>
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $tweets->links() }}
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
