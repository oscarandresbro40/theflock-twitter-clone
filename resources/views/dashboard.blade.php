<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Tweets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('follow'))
                <div class="rounded-md bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first('follow') }}
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(20rem,1fr)]">
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <form method="POST" action="{{ route('tweets.store') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <x-input-label for="body" :value="__('What is happening?')" />
                                    <textarea
                                        id="body"
                                        name="body"
                                        rows="4"
                                        maxlength="280"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >{{ old('body') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('body')" />
                                    <p class="mt-2 text-sm text-gray-500">{{ __('Tweets can be up to 280 characters.') }}</p>
                                </div>

                                <div class="flex justify-end">
                                    <x-primary-button>{{ __('Post tweet') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Your latest tweets') }}</h3>

                            @if ($tweets->isEmpty())
                                <p class="mt-4 text-sm text-gray-600">{{ __('You have not posted any tweets yet.') }}</p>
                            @else
                                <div class="mt-4 space-y-4">
                                    @foreach ($tweets as $tweet)
                                        <article class="rounded-lg border border-gray-200 p-4">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <p class="whitespace-pre-wrap text-sm text-gray-900">{{ $tweet->body }}</p>
                                                    <p class="mt-2 text-xs text-gray-500">{{ $tweet->created_at->format('M j, Y g:i A') }}</p>
                                                    <div class="mt-3 flex items-center gap-3">
                                                        @if ($tweet->liked_by_user)
                                                            <form method="POST" action="{{ route('likes.destroy', $tweet) }}">
                                                                @csrf
                                                                @method('DELETE')

                                                                <x-secondary-button>{{ __('Unlike') }}</x-secondary-button>
                                                            </form>
                                                        @else
                                                            <form method="POST" action="{{ route('likes.store', $tweet) }}">
                                                                @csrf

                                                                <x-primary-button>{{ __('Like') }}</x-primary-button>
                                                            </form>
                                                        @endif

                                                        <p class="text-sm text-gray-600">{{ trans_choice('{0} 0 likes|{1} 1 like|[2,*] :count likes', $tweet->likes_count, ['count' => $tweet->likes_count]) }}</p>
                                                    </div>
                                                </div>

                                                <form method="POST" action="{{ route('tweets.destroy', $tweet) }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <x-danger-button>{{ __('Delete') }}</x-danger-button>
                                                </form>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <aside class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('People to follow') }}</h3>

                        @if ($users->isEmpty())
                            <p class="mt-4 text-sm text-gray-600">{{ __('No other users are available yet.') }}</p>
                        @else
                            <div class="mt-4 space-y-3">
                                @foreach ($users as $user)
                                    <div class="flex items-center justify-between gap-4 rounded-lg border border-gray-200 p-4">
                                        <div>
                                            <a href="{{ route('users.show', $user) }}" class="text-sm font-medium text-gray-900 hover:underline">{{ $user->username }}</a>
                                            <div class="mt-1 flex items-center gap-3 text-xs text-gray-500">
                                                <a href="{{ route('users.followers', $user) }}" class="hover:text-gray-900">{{ __('Followers') }}</a>
                                                <a href="{{ route('users.following', $user) }}" class="hover:text-gray-900">{{ __('Following') }}</a>
                                            </div>
                                        </div>

                                        @if (auth()->user()->isFollowing($user))
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
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
