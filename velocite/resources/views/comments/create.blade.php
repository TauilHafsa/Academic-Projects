<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Add Comment</h1>
                <p class="text-sm text-gray-600">Rental #{{ $rental->id }} - {{ $rental->bike->title }}</p>
            </div>
            <a href="{{ auth()->id() === $rental->renter_id ? route('rentals.show', $rental->id) : route('partner.rentals.show', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Rental
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ auth()->id() === $rental->renter_id ? route('rentals.comments.store', $rental->id) : route('partner.rentals.comments.store', $rental->id) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Share Your Experience</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Your comment will be shared with the other party once both of you have left comments, or after one week.
                            </p>
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Comment</label>
                            <textarea name="content" id="content" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Share your experience with this rental...">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input id="is_private" name="is_private" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ old('is_private') ? 'checked' : '' }}>
                            <label for="is_private" class="ml-2 block text-sm text-gray-700">
                                Private comment (only visible to you)
                            </label>
                        </div>

                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit Comment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 