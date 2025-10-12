<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $bike->title }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('partner.bikes.edit', $bike) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    {{ __('Edit Listing') }}
                </a>
                <a href="{{ route('partner.bikes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    {{ __('Back to Listings') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Bike images and status -->
                        <div class="md:col-span-2">
                            <div class="relative">
                                <!-- Image gallery -->
                                <div class="flex overflow-x-auto pb-2 space-x-2">
                                    @forelse($bike->images as $image)
                                        <div class="flex-shrink-0 {{ $image->is_primary ? 'order-first' : '' }}">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Bike image" class="h-64 w-80 object-cover rounded-md {{ $image->is_primary ? 'ring-2 ring-blue-500' : '' }}">
                                        </div>
                                    @empty
                                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-md">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endforelse
                                </div>

                                <!-- Status badge and premium -->
                                <div class="mt-4 flex justify-between">
                                    <div>
                                        @if($bike->is_available)
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z" />
                                                </svg>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                </svg>
                                                Archived
                                            </span>
                                        @endif
                                    </div>

                                    @if($premiumListing)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            Premium until {{ $premiumListing->end_date->format('M d, Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Bike details -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Bike Details</h3>
                                <div class="mt-2 grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Category</h4>
                                        <p class="mt-1">{{ $bike->category->name }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Brand & Model</h4>
                                        <p class="mt-1">{{ $bike->brand }} {{ $bike->model }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Year</h4>
                                        <p class="mt-1">{{ $bike->year }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Color</h4>
                                        <p class="mt-1">{{ $bike->color }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Frame Size</h4>
                                        <p class="mt-1">{{ $bike->frame_size ?? 'Not specified' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Condition</h4>
                                        <p class="mt-1">{{ ucfirst(str_replace('_', ' ', $bike->condition)) }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Type</h4>
                                        <p class="mt-1">{{ $bike->is_electric ? 'Electric' : 'Regular' }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Location</h4>
                                        <p class="mt-1">{{ $bike->location }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing information -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Pricing</h3>
                                <div class="mt-2 grid grid-cols-3 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Hourly Rate</h4>
                                        <p class="mt-1">€{{ number_format($bike->hourly_rate, 2) }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Daily Rate</h4>
                                        <p class="mt-1">€{{ number_format($bike->daily_rate, 2) }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Weekly Rate</h4>
                                        <p class="mt-1">{{ $bike->weekly_rate ? '€' . number_format($bike->weekly_rate, 2) : 'Not available' }}</p>
                                    </div>
                                    <div class="col-span-3">
                                        <h4 class="text-sm font-medium text-gray-500">Security Deposit</h4>
                                        <p class="mt-1">{{ $bike->security_deposit ? '€' . number_format($bike->security_deposit, 2) : 'No deposit required' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Description</h3>
                                <div class="mt-2 prose max-w-none">
                                    {{ $bike->description }}
                                </div>
                            </div>
                        </div>

                        <!-- Actions and rentals sidebar -->
                        <div class="md:col-span-1">
                            <!-- Actions -->
                            <div class="bg-gray-50 p-4 rounded-md shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>

                                <div class="space-y-3">
                                    <a href="{{ route('partner.bikes.edit', $bike) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                        Edit Listing
                                    </a>

                                    <a href="{{ route('partner.bikes.availability', $bike) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                        Manage Availability
                                    </a>

                                    <form action="{{ route('partner.bikes.toggle-availability', $bike) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 {{ $bike->is_available ? 'bg-gray-600' : 'bg-green-600' }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:{{ $bike->is_available ? 'bg-gray-700' : 'bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $bike->is_available ? 'gray' : 'green' }}-500">
                                            @if($bike->is_available)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                                </svg>
                                                Archive Listing
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 015.905-.75 1 1 0 001.937-.5A5.002 5.002 0 0010 2z" />
                                                </svg>
                                                Activate Listing
                                            @endif
                                        </button>
                                    </form>

                                    @if(!$premiumListing)
                                        <a href="{{ route('partner.bikes.premium', $bike) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            Upgrade to Premium
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Pending Rental Requests -->
                            <div class="mt-6 bg-yellow-50 p-4 rounded-md shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">
                                    Pending Requests
                                    @if($pendingRentals->count() > 0)
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-yellow-600 rounded-full">{{ $pendingRentals->count() }}</span>
                                    @endif
                                </h3>

                                @if($pendingRentals->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($pendingRentals as $rental)
                                            <div class="bg-white p-3 rounded border border-yellow-200">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <p class="font-medium">{{ $rental->renter->name }}</p>
                                                        <p class="text-sm text-gray-600">
                                                            {{ $rental->start_date->format('M d') }} - {{ $rental->end_date->format('M d, Y') }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            €{{ number_format($rental->total_price, 2) }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                            Review
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-600 text-sm">No pending rental requests.</p>
                                @endif
                            </div>

                            <!-- Active Rentals -->
                            @if($activeRentals->count() > 0)
                                <div class="mt-6 bg-blue-50 p-4 rounded-md shadow-sm">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                                        Active Rentals
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-blue-600 rounded-full">{{ $activeRentals->count() }}</span>
                                    </h3>

                                    <div class="space-y-3">
                                        @foreach($activeRentals as $rental)
                                            <div class="bg-white p-3 rounded border border-blue-200">
                                                <div class="flex justify-between">
                                                    <div>
                                                        <p class="font-medium">{{ $rental->renter->name }}</p>
                                                        <p class="text-sm text-gray-600">
                                                            {{ $rental->start_date->format('M d') }} - {{ $rental->end_date->format('M d, Y') }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            €{{ number_format($rental->total_price, 2) }}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                            Details
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
