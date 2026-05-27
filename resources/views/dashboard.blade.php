<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Tweets') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

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
    </div>
</x-app-layout>
