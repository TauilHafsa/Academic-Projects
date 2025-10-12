<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Bike Locations</h1>
                <p class="text-gray-600">{{ count($bikes) }} {{ Str::plural('bike', count($bikes)) }} found</p>
            </div>
            <div class="mt-2 sm:mt-0 flex space-x-3">
                <a href="{{ route('search.index', request()->query()) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    List View
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-7-7v14" />
                    </svg>
                    Home
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Filters Panel -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Filters</h2>
                <form action="{{ route('search.map') }}" method="GET" class="space-y-4">
                    <!-- Search input -->
                    <div>
                        <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Search bikes...">
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
                            <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                min="0" placeholder="Min">
                            <span class="text-gray-500">to</span>
                            <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}"
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

                    <div class="pt-3 flex space-x-2">
                        <button type="submit" class="flex-1 bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Apply Filters
                        </button>
                        <a href="{{ route('search.map') }}" class="flex-1 bg-gray-200 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 text-center">
                            Reset
                        </a>
                    </div>
                </form>

                <!-- Bikes List Preview -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Bikes</h3>

                    @if($bikes->isEmpty())
                        <div class="text-center py-4">
                            <p class="text-gray-600">No bikes found matching your criteria.</p>
                        </div>
                    @else
                        <div class="space-y-4 max-h-[30rem] overflow-y-auto pr-2">
                            @foreach($bikes as $bike)
                                <div id="bike-item-{{ $bike->id }}" class="bike-item border border-gray-200 rounded-lg p-3 hover:bg-blue-50 cursor-pointer transition"
                                     onclick="highlightMarker({{ $bike->id }}, {{ $bike->latitude }}, {{ $bike->longitude }})">
                                    <div class="flex">
                                        <div class="h-24 w-24 bg-gray-200 rounded-md overflow-hidden mr-4">
                                            @if($bike->primaryImage)
                                                <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}" alt="{{ $bike->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $bike->title }}</h4>
                                            <p class="text-xs text-gray-500">{{ $bike->location }}</p>
                                            <div class="mt-1 flex justify-between items-center">
                                                <span class="text-sm font-medium text-blue-600">€{{ number_format($bike->daily_rate, 2) }}</span>
                                                <a href="{{ route('bikes.show', $bike->id) }}" class="text-xs text-blue-600 hover:text-blue-800">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Map Container -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden" style="height: 700px;">
                    <div id="map" class="w-full h-full"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var map;
        var markers = {};
        var activeMarker = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            map = L.map('map').setView([48.8566, 2.3522], 12); // Default to Paris (adjust as needed)

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Create custom marker icon
            var bikeIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            var activeIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            // Add bike markers
            var bounds = L.latLngBounds();
            var hasValidCoordinates = false;

            @foreach($bikes as $bike)
                @if($bike->latitude && $bike->longitude)
                    hasValidCoordinates = true;
                    var marker = L.marker([{{ $bike->latitude }}, {{ $bike->longitude }}], {
                        icon: bikeIcon,
                        bikeId: {{ $bike->id }}
                    }).addTo(map);

                    marker.bindPopup(`
                        <div class="text-center">
                            <h3 class="font-medium text-blue-600">{{ $bike->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $bike->category->name }}</p>
                            <p class="text-sm font-medium">€{{ number_format($bike->daily_rate, 2) }}/day</p>
                            <a href="{{ route('bikes.show', $bike->id) }}" class="inline-block mt-2 px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">View Details</a>
                        </div>
                    `);

                    marker.on('click', function() {
                        highlightBikeItem({{ $bike->id }});
                    });

                    bounds.extend([{{ $bike->latitude }}, {{ $bike->longitude }}]);
                    markers[{{ $bike->id }}] = marker;
                @endif
            @endforeach

            if (hasValidCoordinates) {
                map.fitBounds(bounds, {padding: [50, 50]});
            }
        });

        function highlightMarker(bikeId, lat, lng) {
            // Reset all markers to default
            Object.values(markers).forEach(marker => {
                marker.setIcon(L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }));
            });

            // Highlight selected marker
            if (markers[bikeId]) {
                markers[bikeId].setIcon(L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                }));

                markers[bikeId].openPopup();
                map.panTo([lat, lng]);
            }

            // Highlight bike item in the list
            highlightBikeItem(bikeId);
        }

        function highlightBikeItem(bikeId) {
            // Reset all bike items to default
            document.querySelectorAll('.bike-item').forEach(item => {
                item.classList.remove('bg-blue-50', 'border-blue-500');
                item.classList.add('border-gray-200');
            });

            // Highlight selected bike item
            const bikeItem = document.getElementById(`bike-item-${bikeId}`);
            if (bikeItem) {
                bikeItem.classList.remove('border-gray-200');
                bikeItem.classList.add('bg-blue-50', 'border-blue-500');
                bikeItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }
    </script>
    @endpush
</x-app-layout>
