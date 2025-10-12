<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Vélocité - Bike Rental Platform</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">Vélocité</a>

                    <div class="flex space-x-6">
            @if (Route::has('login'))
                    @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-600 hover:text-indigo-500 transition-colors">Dashboard</a>
                    @else
                                <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-500 transition-colors">Log in</a>

                        @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-gray-600 hover:text-indigo-500 transition-colors">Register</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section with Search -->
        <section class="bg-gradient-to-r from-indigo-50 to-blue-50 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Rent a Bike with</span>
                        <span class="block text-indigo-600">Vélocité</span>
                    </h1>
                    <p class="mt-4 max-w-md mx-auto text-lg text-gray-600 sm:text-xl md:mt-6 md:max-w-3xl">
                        Explore the city on two wheels. Find the perfect bike for your needs, whether it's for commuting, exercise, or adventure.
                    </p>

                    <!-- Search Form -->
                    <div class="mt-12 max-w-2xl mx-auto">
                        <form action="{{ route('search.index') }}" method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:gap-4">
                            <div class="flex-1">
                                <label for="q" class="sr-only">Search bikes</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" name="q" id="q" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Find bikes by name, type, or location...">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <select name="category_id" class="block w-full py-3 pl-3 pr-10 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <select name="location" class="block w-full py-3 pl-3 pr-10 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="">All Locations</option>
                                        @foreach($popularLocations as $location)
                                            <option value="{{ $location }}">{{ $location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="w-full sm:w-auto px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
                                Search
                            </button>
                        </form>

                        <div class="mt-6 text-sm text-gray-600 flex justify-center space-x-6">
                            <a href="{{ route('search.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                Advanced Search
                            </a>
                            <a href="{{ route('search.map') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                Map View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section avec hover effect amélioré -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 font-serif">Bike Categories</h2>
                    <p class="mt-3 max-w-2xl mx-auto text-gray-500">Choose from our wide range of bike categories for every type of rider</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    @foreach($categories as $category)
                        @php
                            $images = [
                                'Road Bike' => 'https://i.pinimg.com/236x/a4/14/8c/a4148cc38eb9dc229a15ba911287b3c1.jpg',
                                'Mountain Bike' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS8Y7yg0SjMElhG2B81CDAHq3qPDU7IyeAJ_w&s',
                                'City Bike' => 'https://a-bike.nl/wp-content/uploads/2017/11/a-bike-city-bike-woman-happy-to-cycle-iconic-bridge-amsterdam.jpg',
                                'Electric Bike' => 'https://travelwest.info/app/uploads/2024/06/Ecargo-image-1024x576.jpg',
                                'Hybrid Bike' => 'https://hybridbikes.co.nz/wp-content/uploads/2024/12/20240928_102219-1-jpg.webp',
                                'Folding Bike' => 'https://hips.hearstapps.com/hmg-prod/images/ride-folding-bikes-6491fb43ae3de.jpg?crop=0.502xw:1.00xh;0.179xw,0&resize=1200:*',
                                'Cargo Bike' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSHmN0X27BxHsBH3SEg2J0ewlDpg6STd7b37A&s',
                                'Children\'s Bike' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrgkd8sxM0xfuqx193lw88QeV77hU6OGRPUQ&s',
                                'Tandem Bike' => 'https://brightspotcdn.byu.edu/dims4/default/b2d9176/2147483647/strip/true/crop/1080x1080+0+0/resize/840x840!/quality/90/?url=https%3A%2F%2Fbrigham-young-brightspot-us-east-2.s3.us-east-2.amazonaws.com%2Fd4%2F7f%2F43aa7f2ed657fedad3c687654acb%2F329087811-934347037978199-6429814563526290585-n.jpg',
                            ];
                            $img = $images[$category->name] ?? 'https://cdn.pixabay.com/photo/2017/02/01/12/50/bicycle-2026260_1280.jpg';
                        @endphp
                        <a href="{{ route('search.index', ['category_id' => $category->id]) }}"
                        class="group bg-white rounded-xl shadow-md hover:shadow-xl border border-gray-100 hover:border-indigo-200 transition transform hover:-translate-y-1">
                            <div class="h-40 w-full rounded-t-xl overflow-hidden">
                                <img src="{{ $img }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-semibold text-gray-900 text-center group-hover:text-indigo-600 transition-colors duration-300">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-gray-500 mt-2 text-center group-hover:text-gray-700 transition-colors duration-300">
                                    {{ Str::limit($category->description, 100) }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Featured Bikes Section -->
        @if(isset($featuredBikes) && $featuredBikes->count() > 0)
            <section class="py-16 bg-gray-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-10 text-center">Featured Bikes</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach($featuredBikes as $bike)
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                                <a href="{{ route('bikes.show', $bike->id) }}" class="block">
                                    <div class="h-56 bg-gray-100 relative">
                                        @if($bike->primaryImage)
                                            <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}"
                                                alt="{{ $bike->title }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                                <span class="text-gray-500">No image available</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 right-3 bg-indigo-600 text-white py-1 px-3 text-xs font-semibold rounded-full">
                                            Featured
                                        </div>
                                    </div>
                                </a>
                                <div class="p-5">
                                    <div class="flex justify-between items-start">
                                        <a href="{{ route('bikes.show', $bike->id) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-600 transition-colors">{{ $bike->title }}</a>
                                        <span class="bg-indigo-100 text-indigo-800 text-xs px-2.5 py-0.5 rounded-full">{{ $bike->category->name }}</span>
                                    </div>
                                    <p class="text-gray-500 text-sm mt-3">{{ Str::limit($bike->description, 100) }}</p>
                                    <div class="flex justify-between items-center mt-5">
                                        <span class="text-indigo-600 font-semibold">€{{ number_format($bike->daily_rate, 2) }} <span class="text-gray-500 text-sm font-normal">/ day</span></span>
                                        <a href="{{ route('bikes.show', $bike->id) }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors">View Details →</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- All Bikes Section -->
        <section id="bikes" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4 sm:mb-0">Available Bikes</h2>
                    <a href="{{ route('search.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        View All Bikes
                    </a>
                </div>

                @if(isset($bikes) && $bikes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        @foreach($bikes as $bike)
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                <a href="{{ route('bikes.show', $bike->id) }}" class="block">
                                    <div class="h-56 bg-gray-100 relative">
                                        @if($bike->primaryImage)
                                            <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}"
                                                alt="{{ $bike->title }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                                <span class="text-gray-500">No image available</span>
                                            </div>
                                        @endif
                                        @if($bike->is_electric)
                                            <div class="absolute top-3 right-3 bg-green-600 text-white py-1 px-2.5 text-xs font-semibold rounded-full">
                                                Electric
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <div class="p-5">
                                    <div class="flex justify-between items-start">
                                        <a href="{{ route('bikes.show', $bike->id) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-600 transition-colors">{{ $bike->title }}</a>
                                        <span class="text-sm text-gray-500">{{ $bike->location }}</span>
                                    </div>
                                    <div class="flex items-center mt-3 space-x-2">
                                        <div class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="text-sm text-gray-600 ml-1">
                                                {{ number_format($bike->average_rating, 1) }} ({{ $bike->rating_count }})
                                            </span>
                                        </div>
                                        <span class="text-gray-300">|</span>
                                        <span class="text-sm text-gray-600">{{ $bike->category->name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-5">
                                        <div>
                                            <span class="text-indigo-600 font-semibold">€{{ number_format($bike->daily_rate, 2) }}</span>
                                            <span class="text-gray-500 text-sm">/ day</span>
                                        </div>
                                        <a href="{{ route('bikes.show', $bike->id) }}" class="text-sm bg-indigo-600 text-white py-1.5 px-4 rounded-md hover:bg-indigo-700 transition-colors">View</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <p class="text-gray-500">No bikes available at the moment. Please check back later.</p>
                    </div>
                @endif
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">How It Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="text-center">
                        <div class="bg-indigo-100 rounded-full h-20 w-20 flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">1. Find a Bike</h3>
                        <p class="text-gray-500">Browse our selection of bikes and choose the one that fits your needs.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-indigo-100 rounded-full h-20 w-20 flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">2. Book Your Rental</h3>
                        <p class="text-gray-500">Select your dates and request the bike. Owner will confirm your booking.</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-indigo-100 rounded-full h-20 w-20 flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">3. Enjoy Your Ride</h3>
                        <p class="text-gray-500">Pick up the bike, explore, and drop it off at the end of your rental period.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-indigo-600">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-extrabold text-white mb-5">Ready to start riding?</h2>
                <p class="text-xl text-indigo-100 mb-8">Join Vélocité today and discover the freedom of cycling.</p>
                <div class="inline-flex rounded-md shadow">
                    @guest
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 transition-colors">
                            Sign Up Now
                        </a>
                    @else
                        <a href="{{ route('search.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 transition-colors">
                            Find a Bike
                        </a>
                    @endguest
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white py-8 border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <p class="text-gray-500">&copy; {{ date('Y') }} Vélocité. All rights reserved.</p>
                    </div>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors">About</a>
                        <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors">Privacy</a>
                        <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors">Terms</a>
                        <a href="#" class="text-gray-500 hover:text-indigo-600 transition-colors">Contact</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>