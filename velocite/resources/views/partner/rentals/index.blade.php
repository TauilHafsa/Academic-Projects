<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Rental Management</h1>

        <!-- Status Filters and Counts -->
        <div class="mb-6 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="flex flex-wrap border-b border-gray-200">
                <a href="{{ route('partner.rentals.index') }}" class="px-6 py-3 text-sm font-medium {{ !request('status') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    All <span class="ml-1 text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">{{ array_sum($statusCounts) }}</span>
                </a>
                <a href="{{ route('partner.rentals.index', ['status' => 'pending']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'pending' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Pending <span class="ml-1 text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full">{{ $statusCounts['pending'] }}</span>
                </a>
                <a href="{{ route('partner.rentals.index', ['status' => 'confirmed']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'confirmed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Confirmed <span class="ml-1 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">{{ $statusCounts['confirmed'] }}</span>
                </a>
                <a href="{{ route('partner.rentals.index', ['status' => 'ongoing']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'ongoing' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Ongoing <span class="ml-1 text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">{{ $statusCounts['ongoing'] }}</span>
                </a>
                <a href="{{ route('partner.rentals.index', ['status' => 'completed']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'completed' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Completed <span class="ml-1 text-xs bg-gray-100 text-gray-800 px-2 py-0.5 rounded-full">{{ $statusCounts['completed'] }}</span>
                </a>
                <a href="{{ route('partner.rentals.index', ['status' => 'cancelled']) }}" class="px-6 py-3 text-sm font-medium {{ request('status') == 'cancelled' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-800' }}">
                    Cancelled <span class="ml-1 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">{{ $statusCounts['cancelled'] }}</span>
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
                                <div class="w-full h-24 bg-gray-200 rounded-md relative overflow-hidden">
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
                                        <p class="text-sm text-gray-500">Requested by: {{ $rental->renter->name }}</p>
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
                                        <p class="text-xs text-gray-500">Rental Period</p>
                                        <p class="text-sm text-gray-800">{{ $rental->start_date->format('M d, Y') }} - {{ $rental->end_date->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Price</p>
                                        <p class="text-sm font-semibold text-gray-800">€{{ number_format($rental->total_price, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Request Date</p>
                                        <p class="text-sm text-gray-800">{{ $rental->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap items-center justify-between border-t border-gray-200 pt-4">
                                    <div class="text-sm text-gray-600">
                                        @if($rental->status === 'pending')
                                            <span class="text-yellow-600">Needs your approval</span>
                                        @elseif($rental->status === 'confirmed' && $rental->start_date->isPast() && $rental->end_date->isFuture())
                                            <span class="text-blue-600">Ready to start</span>
                                        @elseif($rental->status === 'ongoing' && $rental->end_date->isPast())
                                            <span class="text-green-600">Ready to complete</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 sm:mt-0 flex space-x-2">
                                        <a href="{{ route('partner.rentals.show', $rental->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm leading-4 font-medium rounded-md bg-white text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200">
                                            View Details
                                        </a>

                                        @if($rental->status === 'pending')
                                            <form action="{{ route('partner.rentals.approve', $rental->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200">
                                                    Approve
                                                </button>
                                            </form>

                                            <button type="button" onclick="document.getElementById('reject-modal-{{ $rental->id }}').classList.remove('hidden')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200">
                                                Reject
                                            </button>
                                        @endif

                                        @if($rental->status === 'confirmed')
                                            <form action="{{ route('partner.rentals.start', $rental->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200">
                                                    Start Rental
                                                </button>
                                            </form>
                                        @endif

                                        @if($rental->status === 'ongoing')
                                            <form action="{{ route('partner.rentals.complete', $rental->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200">
                                                    Complete Rental
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rejection Modal -->
                        <div id="reject-modal-{{ $rental->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                <div class="mt-3 text-center">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Reject Rental Request</h3>
                                    <div class="mt-2 px-7 py-3">
                                        <form action="{{ route('partner.rentals.reject', $rental->id) }}" method="POST">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 text-left">Reason (optional)</label>
                                                <textarea name="rejection_reason" id="rejection_reason" rows="3" class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                            </div>
                                            <div class="flex justify-between mt-4">
                                                <button type="button" onclick="document.getElementById('reject-modal-{{ $rental->id }}').classList.add('hidden')" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md bg-white text-gray-700 hover:bg-gray-50 focus:outline-none">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-500 focus:outline-none">
                                                    Reject Request
                                                </button>
                                            </div>
                                        </form>
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
                <p class="text-gray-600 mb-4">{{ request('status') ? 'You don\'t have any '.request('status').' rentals.' : 'You haven\'t received any rental requests yet.' }}</p>

                <a href="{{ route('partner.bikes.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Manage Your Bikes
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
