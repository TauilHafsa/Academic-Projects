<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Partner Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Bike Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Your Bikes</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-bold text-gray-900">{{ $totalBikes }}</div>
                        <p class="text-gray-600">Total Bikes</p>
                    </div>
                    <div class="mt-2">
                        <div class="text-sm font-medium text-gray-600">{{ $activeBikes }} Active / {{ $totalBikes - $activeBikes }} Archived</div>
                        <div class="mt-1 w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $totalBikes > 0 ? ($activeBikes / $totalBikes) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('partner.bikes.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Manage Bikes →
                        </a>
                    </div>
                </div>

                <!-- Rental Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Rentals</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-bold text-gray-900">{{ $pendingRentals }}</div>
                        <p class="text-gray-600">Pending Requests</p>
                    </div>
                    <div class="mt-2">
                        <div class="text-xl font-bold text-gray-900">{{ $activeRentals }}</div>
                        <p class="text-gray-600">Active Rentals</p>
                    </div>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Rentals →
                        </a>
                    </div>
                </div>

                <!-- Earnings Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Earnings</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-bold text-gray-900">€{{ number_format($totalEarnings, 2) }}</div>
                        <p class="text-gray-600">Total Earnings</p>
                    </div>
                    <div class="mt-2">
                        <div class="text-xl font-bold text-gray-900">€{{ number_format($monthlyEarnings, 2) }}</div>
                        <p class="text-gray-600">This Month</p>
                    </div>
                    <div class="mt-4">
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Earnings →
                        </a>
                    </div>
                </div>

                <!-- Action Box -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 overflow-hidden shadow-sm sm:rounded-lg p-6 text-white">
                    <h3 class="text-lg font-medium">Quick Actions</h3>
                    <p class="mt-1 text-sm text-blue-100">Manage your bike rentals</p>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('partner.bikes.create') }}" class="block w-full py-2 px-3 bg-white text-blue-600 rounded-md text-sm font-medium hover:bg-blue-50 transition-colors">
                            Add New Bike
                        </a>
                        <a href="{{ route('partner.bikes.index') }}" class="block w-full py-2 px-3 bg-blue-400 text-white rounded-md text-sm font-medium hover:bg-blue-300 transition-colors">
                            Manage Your Bikes
                        </a>
                        <a href="#" class="block w-full py-2 px-3 bg-blue-400 text-white rounded-md text-sm font-medium hover:bg-blue-300 transition-colors">
                            View Rental Requests
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Bikes & Popular Bikes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Recent Bikes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Bikes</h3>
                        <a href="{{ route('partner.bikes.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>

                    @if($recentBikes->isEmpty())
                        <div class="text-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-gray-600">No bikes added yet</p>
                            <a href="{{ route('partner.bikes.create') }}" class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Add Your First Bike
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($recentBikes as $bike)
                                <div class="flex items-center border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                    <div class="w-16 h-16 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                                        @if($bike->primaryImage)
                                            <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}" alt="{{ $bike->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $bike->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $bike->brand }} {{ $bike->model }} ({{ $bike->year }})</p>
                                        <p class="text-sm text-gray-600">€{{ number_format($bike->daily_rate, 2) }}/day</p>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bike->is_available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $bike->is_available ? 'Active' : 'Archived' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Popular Bikes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Popular Bikes</h3>
                        <a href="{{ route('partner.bikes.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                    </div>

                    @if($popularBikes->isEmpty())
                        <div class="text-center py-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <p class="mt-2 text-gray-600">No rental data available yet</p>
                            <p class="text-sm text-gray-500">As your bikes get rented, we'll show the most popular ones here</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($popularBikes as $bike)
                                <div class="flex items-center border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                                    <div class="w-16 h-16 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                                        @if($bike->primaryImage)
                                            <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}" alt="{{ $bike->title }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $bike->title }}</h4>
                                        <p class="text-sm text-gray-600">{{ $bike->brand }} {{ $bike->model }} ({{ $bike->year }})</p>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-lg font-medium text-blue-600">{{ $bike->rentals_count }}</span>
                                        <p class="text-xs text-gray-500">rentals</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Rental Requests -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Recent Rental Requests</h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>

                @if($recentRentals->isEmpty())
                    <div class="text-center py-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-gray-600">No rental requests yet</p>
                        <p class="text-sm text-gray-500">When someone wants to rent your bikes, requests will appear here</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Renter
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bike
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dates
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentRentals as $rental)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if($rental->renter->profile && $rental->renter->profile->profile_picture)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $rental->renter->profile->profile_picture) }}" alt="{{ $rental->renter->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $rental->renter->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $rental->renter->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $rental->bike->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $rental->bike->brand }} {{ $rental->bike->model }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $rental->start_date->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">to {{ $rental->end_date->format('M d, Y') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            €{{ number_format($rental->total_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($rental->status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif($rental->status == 'confirmed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Confirmed
                                                </span>
                                            @elseif($rental->status == 'ongoing')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    Ongoing
                                                </span>
                                            @elseif($rental->status == 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Completed
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    {{ ucfirst($rental->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="#" class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
