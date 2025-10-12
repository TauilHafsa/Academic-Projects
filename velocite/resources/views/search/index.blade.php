<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start gap-6">
            <!-- Filter Sidebar -->
            <div class="w-full md:w-64 bg-white p-6 rounded-lg shadow-md mb-6 md:mb-0 flex-shrink-0">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
                <form action="{{ route('search.index') }}" method="GET" class="space-y-4">
                    <!-- Search input -->
                    <div>
                        <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Search bikes...">
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <select name="location" id="location"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Locations</option>
                            @foreach($popularLocations as $location)
                                <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                    {{ $location }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" id="category_id"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Price Range -->
                    <div class="space-y-2">
                        <h3 class="text-sm font-medium text-gray-700">Price Range (Daily)</h3>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="min_price" id="min_price" value="{{ request('min_price', $priceRange->min_price ?? 0) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="0" placeholder="Min">
                            <span class="text-gray-500">to</span>
                            <input type="number" name="max_price" id="max_price" value="{{ request('max_price', $priceRange->max_price ?? 1000) }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="0" placeholder="Max">
                        </div>
                    </div>

                    <!-- Electric Bikes -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_electric" id="is_electric" value="1"
                            {{ request('is_electric') == 1 ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_electric" class="ml-2 block text-sm text-gray-700">Electric Bikes Only</label>
                    </div>

                    <!-- Rating -->
                    <div>
                        <label for="min_rating" class="block text-sm font-medium text-gray-700 mb-1">Minimum Rating</label>
                        <select name="min_rating" id="min_rating"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Any Rating</option>
                            <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+</option>
                            <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+</option>
                            <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="space-y-2">
                        <h3 class="text-sm font-medium text-gray-700">Available Dates</h3>
                        <div class="space-y-2">
                            <label for="start_date" class="block text-xs text-gray-500">From</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="space-y-2">
                            <label for="end_date" class="block text-xs text-gray-500">To</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filters
                        </button>
                        <a href="{{ route('search.index') }}" class="flex-1 bg-gray-200 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Results -->
            <div class="flex-1">
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h1 class="text-xl font-semibold text-gray-900">
                            {{ request('q') ? 'Search Results for "' . request('q') . '"' : 'Available Bikes' }}
                        </h1>
                        <div class="mt-2 sm:mt-0 flex items-center space-x-2">
                            <a href="{{ route('search.map', request()->query()) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Map View
                            </a>
                            <select name="sort_by" id="sort_by" onchange="updateSort(this.value)"
                                class="block rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="newest" {{ request('sort_by') == 'newest' || !request('sort_by') ? 'selected' : '' }}>
                                    Newest
                                </option>
                                <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>
                                    Price: Low to High
                                </option>
                                <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>
                                    Price: High to Low
                                </option>
                                <option value="rating_desc" {{ request('sort_by') == 'rating_desc' ? 'selected' : '' }}>
                                    Highest Rated
                                </option>
                            </select>
                        </div>
                    </div>

                    <p class="text-gray-500 text-sm mb-4">
                        {{ $bikes->total() }} {{ Str::plural('bike', $bikes->total()) }} found
                    </p>

                    @if($bikes->isEmpty())
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No bikes found</h3>
                            <p class="text-gray-600 mb-4">Try adjusting your filters to find more bikes.</p>
                            <a href="{{ route('search.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Reset Filters
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($bikes as $bike)
                                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                    <a href="{{ route('bikes.show', $bike->id) }}" class="block">
                                        <div class="h-48 bg-gray-200 relative">
                                            @if($bike->primaryImage)
                                                <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}"
                                                    alt="{{ $bike->title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-gray-400">No image available</span>
                                                </div>
                                            @endif

                                            @if($bike->is_electric)
                                                <div class="absolute top-0 right-0 bg-green-600 text-white py-1 px-3 text-xs font-semibold">
                                                    Electric
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start">
                                            <a href="{{ route('bikes.show', $bike->id) }}" class="text-lg font-semibold text-gray-900 hover:text-blue-600">
                                                {{ $bike->title }}
                                            </a>
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                {{ $bike->category->name }}
                                            </span>
                                        </div>

                                        <div class="mt-1 text-sm text-gray-500">
                                            {{ $bike->location }}
                                        </div>

                                        <div class="flex items-center mt-2">
                                            <span class="text-yellow-400 mr-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                {{ number_format($bike->average_rating, 1) }} ({{ $bike->rating_count }})
                                            </span>
                                        </div>

                                        <div class="mt-4 flex justify-between items-center">
                                            <div>
                                                <span class="text-blue-600 font-semibold">€{{ number_format($bike->daily_rate, 2) }}</span>
                                                <span class="text-gray-600 text-sm">/ day</span>
                                            </div>
                                            <a href="{{ route('bikes.show', $bike->id) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $bikes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateSort(value) {
            const url = new URL(window.location);
            url.searchParams.set('sort_by', value);
            window.location = url.toString();
        }
    </script>
</x-app-layout>
