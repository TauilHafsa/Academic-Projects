<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Rate the Bike Owner</h1>
                <p class="text-sm text-gray-600">Rental #{{ $rental->id }} - {{ $rental->bike->owner->name }}</p>
            </div>
            <a href="{{ route('rentals.show', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Rental
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('rentals.rate.user', $rental->id) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Rate Your Experience</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Please provide a rating based on your experience with this bike owner.
                            </p>
                        </div>

                        <div>
                            <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Rating (1-5)</label>
                            <select name="rating" id="rating" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Select a rating</option>
                                <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 - Poor</option>
                                <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 - Fair</option>
                                <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 - Good</option>
                                <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 - Very Good</option>
                                <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>5 - Excellent</option>
                            </select>
                            @error('rating')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="review" class="block text-sm font-medium text-gray-700 mb-1">Review (optional)</label>
                            <textarea name="review" id="review" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('review') }}</textarea>
                            @error('review')
                                <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit Rating
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>