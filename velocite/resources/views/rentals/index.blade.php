<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">My Rentals</h1>

        <!-- Status Filters -->
        <div class="mb-6 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="flex flex-wrap border-b border-gray-200">
                <a href="{{ route('rentals.index') }}" class="px-6 py-3 text-sm font-medium {{ !request('status') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    All
                </a>
                <a href="{{ route('rentals.index', ['status' => 'pending']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'pending' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Pending
                </a>
                <a href="{{ route('rentals.index', ['status' => 'confirmed']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'confirmed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Confirmed
                </a>
                <a href="{{ route('rentals.index', ['status' => 'ongoing']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'ongoing' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Ongoing
                </a>
                <a href="{{ route('rentals.index', ['status' => 'completed']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'completed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Completed
                </a>
                <a href="{{ route('rentals.index', ['status' => 'cancelled']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'cancelled' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Cancelled
                </a>
            </div>
        </div>

        <!-- Rentals List -->
        @if($rentals->count() > 0)
            <div class="space-y-4">
                @foreach($rentals as $rental)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="sm:flex items-center p-4">
                            <div class="mb-4 sm:mb-0 sm:mr-6 sm:w-32 lg:w-48 flex-shrink-0">
                                <div class="h-24 w-24 bg-gray-200 rounded-md overflow-hidden">
                                    @if($rental->bike->primaryImage)
                                        <img src="{{ asset('storage/' . $rental->bike->primaryImage->image_path) }}" alt="{{ $rental->bike->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-gray-400">No image</span>
                                        </div>
                                    @endif

                                    @if($rental->bike->is_electric)
                                        <span class="absolute top-0 right-0 bg-green-500 text-white text-xs px-2 py-1">Electric</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2">
                                    <div>
                                        <h2 class="text-lg font-semibold text-gray-900">{{ $rental->bike->title }}</h2>
                                        <p class="text-sm text-gray-600">{{ $rental->bike->category->name }}</p>
                                    </div>
                                    <div class="mt-1 sm:mt-0">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-blue-100 text-blue-800',
                                                'ongoing' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusColor = $statusColors[$rental->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst($rental->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Location</p>
                                        <p class="text-sm text-gray-800">{{ $rental->bike->location }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Rental Period</p>
                                        <p class="text-sm text-gray-800">{{ $rental->start_date->format('M d, Y') }} - {{ $rental->end_date->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Price</p>
                                        <p class="text-sm font-semibold text-gray-800">€{{ number_format($rental->total_price, 2) }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between border-t border-gray-200 pt-4">
                                    <div class="text-sm text-gray-600">
                                        Owner: <span class="font-medium">{{ $rental->bike->owner->name }}</span>
                                    </div>
                                    <div class="mt-2 sm:mt-0 flex space-x-2">
                                        <a href="{{ route('rentals.show', $rental->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm leading-4 font-medium rounded-md bg-white text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200">
                                            View Details
                                        </a>

                                        @if($rental->status === 'pending')
                                            <form action="{{ route('rentals.cancel', $rental->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200" onclick="return confirm('Are you sure you want to cancel this rental?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif

                                        @if($rental->status === 'completed' && !$rental->bikeRating()->exists())
                                            <a href="{{ route('rentals.show', $rental->id) }}#rate" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200">
                                                Rate
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $rentals->links() }}
                </div>
            </div>
        @else
            <div class="bg-white p-6 rounded-lg shadow-sm text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>

                <h3 class="text-lg font-medium text-gray-900 mb-2">No rentals found</h3>
                <p class="text-gray-600 mb-4">{{ request('status') ? 'You don\'t have any '.request('status').' rentals.' : 'You haven\'t rented any bikes yet.' }}</p>

                <a href="{{ route('search.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Find Bikes to Rent
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
