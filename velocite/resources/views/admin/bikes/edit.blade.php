<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">Edit Bike</h1>
                            <p class="text-gray-600">Update bike information for {{ $bike->title }}</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.bikes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Bikes
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Bike Information</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Update the bike's information and settings.</p>
                        </div>

                        <form action="{{ route('admin.bikes.update', $bike->id) }}" method="POST" class="p-6">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700">
                                                Please correct the following errors:
                                            </p>
                                            <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Bike Basic Information -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Basic Information</h4>

                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                                        <input type="text" id="title" name="title" value="{{ old('title', $bike->title) }}" required
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea id="description" name="description" rows="4" required
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $bike->description) }}</textarea>
                                    </div>

                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                        <select id="category_id" name="category_id" required
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ (old('category_id', $bike->category_id) == $category->id) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                        <input type="text" id="location" name="location" value="{{ old('location', $bike->location) }}" required
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                </div>

                                <!-- Bike Settings and Owner Info -->
                                <div class="space-y-4">
                                    <h4 class="text-md font-medium text-gray-900 mb-2">Settings & Details</h4>

                                    <div>
                                        <label for="daily_rate" class="block text-sm font-medium text-gray-700 mb-1">Daily Rate (€)</label>
                                        <input type="number" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $bike->daily_rate) }}" required min="0" step="0.01"
                                            class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>

                                    <div class="flex items-center">
                                        <input type="hidden" name="is_available" value="0">
                                        <input id="is_available" name="is_available" type="checkbox" value="1" {{ old('is_available', $bike->is_available) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="is_available" class="ml-2 block text-sm text-gray-900">
                                            Available for Rent
                                        </label>
                                    </div>

                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mt-2">
                                        <h4 class="text-sm font-medium text-blue-800 mb-2">Owner Information</h4>
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 font-bold flex-shrink-0">
                                                {{ substr($bike->owner->name, 0, 1) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $bike->owner->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $bike->owner->email }}</div>
                                                <div class="text-xs text-gray-500">{{ $bike->owner->profile->city ?? 'No city' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mt-2">
                                        <h4 class="text-sm font-medium text-gray-800 mb-2">Bike Status</h4>
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Listed:</span>
                                                <span class="text-gray-900">{{ $bike->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Total Rentals:</span>
                                                <span class="text-gray-900">{{ $bike->rentals->count() }}</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Active Rentals:</span>
                                                <span class="text-gray-900">{{ $bike->rentals->whereIn('status', ['confirmed', 'ongoing'])->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bike Images -->
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-900 mb-2">Bike Images</h4>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @if($bike->images->count() > 0)
                                        @foreach($bike->images as $image)
                                            <div class="relative">
                                                <img src="{{ asset('storage/bikes/' . $image->filename) }}" alt="{{ $bike->title }}" class="h-32 w-full object-cover rounded-lg">
                                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 text-center">
                                                    @if($image->is_primary)
                                                        Primary Image
                                                    @else
                                                        Additional Image
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="col-span-4">
                                            <div class="bg-gray-100 rounded-lg p-4 text-center text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="mt-2">No images available for this bike</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <a href="{{ route('admin.bikes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Update Bike
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
