<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Bikes Near You</h1>
                <p class="text-gray-600">{{ count($bikes) }} {{ Str::plural('bike', count($bikes)) }} found within {{ $radius }} {{ $unit }}</p>
            </div>
            <div class="mt-2 sm:mt-0 flex space-x-3">
                <a href="{{ route('search.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Search
                </a>
                <a href="{{ route('search.map', ['latitude' => $latitude, 'longitude' => $longitude, 'radius' => $radius]) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Map View
                </a>
            </div>
        </div>

        <!-- Search Form -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Search Location</h2>
            <form action="{{ route('search.nearby') }}" method="GET" class="flex flex-col sm:flex-row items-start gap-4">
                <div class="w-full sm:w-auto">
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                    <input type="text" name="latitude" id="latitude" value="{{ $latitude }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div class="w-full sm:w-auto">
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                    <input type="text" name="longitude" id="longitude" value="{{ $longitude }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
                <div class="w-full sm:w-auto">
                    <label for="radius" class="block text-sm font-medium text-gray-700 mb-1">Radius ({{ $unit }})</label>
                    <input type="number" name="radius" id="radius" value="{{ $radius }}" min="1" max="100"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="w-full sm:w-auto">
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <select name="unit" id="unit"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="km" {{ $unit == 'km' ? 'selected' : '' }}>Kilometers</option>
                        <option value="mi" {{ $unit == 'mi' ? 'selected' : '' }}>Miles</option>
                    </select>
                </div>
                <div class="self-end pt-1 mt-4 sm:mt-0">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            @if($bikes->isEmpty())
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No bikes found nearby</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your search radius or location.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($bikes as $bike)
                        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            <a href="{{ route('bikes.show', $bike->id) }}" class="block">
                                <div class="h-48 bg-gray-200 rounded-md overflow-hidden">
                                    @if($bike->primaryImage)
                                        <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}" alt="{{ $bike->title }}" class="w-full h-full object-cover">
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

                                    @if(isset($bike->distance))
                                        <div class="absolute bottom-0 left-0 bg-blue-600 text-white py-1 px-3 text-xs font-semibold">
                                            {{ number_format($bike->distance, 1) }} {{ $unit }}
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
            @endif
        </div>
    </div>

    <script>
        // Function to get current location
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                    },
                    function(error) {
                        console.error("Error getting location:", error);
                        alert("Could not get your location. Please enter coordinates manually.");
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Add a button to get current location
        document.addEventListener('DOMContentLoaded', function() {
            const latitudeField = document.getElementById('latitude');
            const longitudeField = document.getElementById('longitude');

            // Create the button
            const locationButton = document.createElement('button');
            locationButton.type = 'button';
            locationButton.className = 'mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
            locationButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Use My Location
            `;
            locationButton.onclick = getCurrentLocation;

            // Insert the button after the longitude field
            longitudeField.parentNode.insertAdjacentElement('afterend', locationButton);
        });
    </script>
</x-app-layout>
