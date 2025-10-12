<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $bike->title }} - Vélocité</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('home') }}" class="text-3xl font-bold text-blue-600">Vélocité</a>

                <div class="flex space-x-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-gray-700 hover:text-blue-600">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Bike Details -->
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Back button -->
        <div class="mb-6">
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to bikes
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Bike Images -->
            <div class="md:col-span-2">
                <div class="bg-gray-100 rounded-lg overflow-hidden mb-4">
                    @if($bike->primaryImage)
                        <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}"
                            alt="{{ $bike->title }}"
                            class="w-full h-full object-cover rounded-lg shadow-md">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 rounded-lg shadow-md">
                            <span class="text-gray-400">No image available</span>
                        </div>
                    @endif
                </div>

                @if($bike->images && $bike->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($bike->images as $image)
                            <div class="bg-gray-100 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    alt="{{ $bike->title }}"
                                    class="w-full h-24 object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Bike Info -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $bike->title }}</h1>
                <div class="flex items-center mb-4">
                    <span class="text-blue-600 font-semibold mr-2">{{ $bike->category->name }}</span>
                    @if($bike->is_electric)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Electric</span>
                    @endif
                </div>

                <div class="border-t border-gray-200 py-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">Hourly Rate</span>
                        <span class="font-bold">€{{ number_format($bike->hourly_rate, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">Daily Rate</span>
                        <span class="font-bold">€{{ number_format($bike->daily_rate, 2) }}</span>
                    </div>
                    @if($bike->weekly_rate)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Weekly Rate</span>
                        <span class="font-bold">€{{ number_format($bike->weekly_rate, 2) }}</span>
                    </div>
                    @endif
                </div>

                <div class="border-t border-gray-200 py-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Bike Details</h3>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-600">Brand:</span>
                            <span class="ml-1">{{ $bike->brand }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Model:</span>
                            <span class="ml-1">{{ $bike->model }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Year:</span>
                            <span class="ml-1">{{ $bike->year }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Color:</span>
                            <span class="ml-1">{{ $bike->color }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Condition:</span>
                            <span class="ml-1">{{ ucfirst(str_replace('_', ' ', $bike->condition)) }}</span>
                        </div>
                        @if($bike->frame_size)
                        <div>
                            <span class="text-gray-600">Frame Size:</span>
                            <span class="ml-1">{{ $bike->frame_size }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="border-t border-gray-200 py-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Location</h3>
                    <p class="text-gray-700">{{ $bike->location }}</p>
                </div>

                <div class="mt-6">
                    @auth
                        @if((auth()->user()->hasRole('client') || auth()->user()->hasRole('partner')) && auth()->id() !== $bike->owner_id)
                            <a href="{{ route('rentals.create', ['bike_id' => $bike->id]) }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg w-full">
                                Request to Rent
                            </a>
                        @elseif(auth()->id() === $bike->owner_id)
                            <div class="text-center bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-lg w-full">
                                You own this bike
                            </div>
                        @else
                            <div class="text-center bg-gray-200 text-gray-700 font-bold py-3 px-4 rounded-lg w-full">
                                You cannot rent bikes with your current role
                            </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg w-full">
                            Log in to Rent
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
            <p class="text-gray-700 whitespace-pre-line">{{ $bike->description }}</p>
        </div>

        <!-- Owner Info -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">About the Owner</h2>
            <div class="flex items-center">
                <div class="mr-4">
                    @if($bike->owner->profile && $bike->owner->profile->profile_picture)
                        <div class="w-16 h-16 rounded-full bg-blue-200 flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-xl">{{ substr($bike->owner->name, 0, 1) }}</span>
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-full bg-blue-200 flex items-center justify-center">
                            <span class="text-blue-600 font-bold text-xl">{{ substr($bike->owner->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $bike->owner->name }}</h3>
                    @if($bike->owner->profile)
                        <p class="text-gray-600">{{ $bike->owner->profile->city }}</p>
                    @endif
                    <div class="flex items-center mt-1">
                        <span class="text-yellow-400 mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </span>
                        <span class="text-gray-700">
                            @if($bike->owner->profile && $bike->owner->profile->rating_count > 0)
                                {{ number_format($bike->owner->profile->average_rating, 1) }} ({{ $bike->owner->profile->rating_count }} reviews)
                            @else
                                New Owner
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ratings -->
        @if($bike->ratings && $bike->ratings->count() > 0)
        <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Reviews ({{ $bike->ratings->count() }})</h2>
            <div class="space-y-4">
                @foreach($bike->ratings as $rating)
                <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                    <div class="flex items-center mb-2">
                        <div class="mr-2">
                            @if($rating->user->profile && $rating->user->profile->profile_picture)
                                <img src="{{ asset('storage/' . $rating->user->profile->profile_picture) }}"
                                    alt="{{ $rating->user->name }}"
                                    class="w-10 h-10 rounded-full object-cover">
                            @else
                                <div class="w-10 h-10 rounded-full bg-blue-200 flex items-center justify-center">
                                    <span class="text-blue-600 font-bold">{{ substr($rating->user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $rating->user->name }}</h4>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating->rating)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endif
                                @endfor
                                <span class="ml-2 text-sm text-gray-500">{{ $rating->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    @if($rating->review)
                        <p class="text-gray-700">{{ $rating->review }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Bike Images Gallery -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">More Photos</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse($bike->images as $image)
                    <div class="overflow-hidden rounded-lg h-40 bg-gray-200">
                        <img src="{{ asset('storage/' . $image->image_path) }}"
                            alt="{{ $bike->title }}"
                            class="w-full h-full object-cover">
                    </div>
                @empty
                    <p class="col-span-4 text-gray-500 text-center py-8">No additional photos available</p>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600">&copy; {{ date('Y') }} Vélocité. All rights reserved.</p>
                </div>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-600 hover:text-blue-600">About</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Terms</a>
                    <a href="#" class="text-gray-600 hover:text-blue-600">Privacy</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
