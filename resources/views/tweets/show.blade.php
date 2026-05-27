<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Flock X Challenge') }}</title>

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
                    <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-900">{{ config('app.name', 'Flock X Challenge') }}</a>

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
                @if (session('status'))
                    <div class="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ session('status') }}
                    </div>
                @endif

                <section class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <h1 class="text-lg font-semibold text-gray-900">{{ __('Tweet') }}</h1>
                        <div class="mt-4 rounded-lg border border-gray-200 p-4">
                            <a href="{{ route('users.show', $tweet->user) }}" class="text-sm font-semibold text-gray-900 hover:underline">{{ '@'.$tweet->user->username }}</a>
                            <p class="mt-2 whitespace-pre-wrap text-sm text-gray-900">{{ $tweet->body }}</p>
                            @if ($tweet->image_path)
                                <img src="{{ asset('storage/'.$tweet->image_path) }}" alt="{{ __('Tweet image') }}" class="mt-3 max-h-80 w-full rounded-lg border border-gray-200 object-cover">
                            @endif
                            <p class="mt-2 text-xs text-gray-500">{{ $tweet->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Replies') }}</h2>

                        @if ($tweet->replies->isEmpty())
                            <p class="mt-4 text-sm text-gray-600">{{ __('No replies yet.') }}</p>
                        @else
                            <div class="mt-4 space-y-4">
                                @foreach ($tweet->replies as $reply)
                                    <article class="rounded-lg border border-gray-200 p-4">
                                        <a href="{{ route('users.show', $reply->user) }}" class="text-sm font-semibold text-gray-900 hover:underline">{{ '@'.$reply->user->username }}</a>
                                        <p class="mt-2 whitespace-pre-wrap text-sm text-gray-900">{{ $reply->body }}</p>
                                        @if ($reply->image_path)
                                            <img src="{{ asset('storage/'.$reply->image_path) }}" alt="{{ __('Tweet image') }}" class="mt-3 max-h-80 w-full rounded-lg border border-gray-200 object-cover">
                                        @endif
                                        <p class="mt-2 text-xs text-gray-500">{{ $reply->created_at->format('M j, Y g:i A') }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <section class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Reply to this tweet') }}</h2>

                        @auth
                            <form method="POST" action="{{ route('tweets.replies.store', $tweet) }}" class="mt-4 space-y-4">
                                @csrf

                                <div>
                                    <x-input-label for="body" :value="__('Your reply')" />
                                    <textarea
                                        id="body"
                                        name="body"
                                        rows="3"
                                        maxlength="280"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >{{ old('body') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('body')" />
                                </div>

                                <div class="flex justify-end">
                                    <x-primary-button>{{ __('Post reply') }}</x-primary-button>
                                </div>
                            </form>
                        @else
                            <p class="mt-4 text-sm text-gray-600">
                                {{ __('Want to reply?') }}
                                <a href="{{ route('login') }}" class="font-medium text-gray-900 underline">{{ __('Log in') }}</a>
                            </p>
                        @endauth
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
