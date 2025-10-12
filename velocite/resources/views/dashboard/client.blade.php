<x-app-layout>
    <style>
        .fade-in { animation: fadeIn 0.6s ease-in-out; }
        .slide-in { animation: slideIn 0.6s ease-in-out; }
        .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
        .card-gradient { background-image: linear-gradient(to right bottom, rgba(255,255,255,0.95), rgba(255,255,255,0.8)); backdrop-filter: blur(5px); }
        .pulse { animation: pulse 2s infinite; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); } 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); } }
        .status-badge { transition: all 0.3s ease; }
        .status-badge:hover { transform: scale(1.1); }
        .partner-banner { 
            background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            transition: all 0.3s ease;
        }
        .partner-banner:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.2);
        }
        .partner-btn {
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        }
        .partner-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mb-6">
            <div class="partner-banner rounded-lg shadow-md fade-in overflow-hidden">
                <div class="p-6 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white p-3 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Become a Partner and Start Earning</h3>
                            <p class="text-green-50">
                                Do you own bikes you'd like to rent out? Join our partner program and start making money today.
                            </p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('become.partner') }}" class="partner-btn inline-flex items-center px-6 py-3 border border-transparent rounded-md font-semibold text-sm text-green-700 uppercase tracking-wider hover:text-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200">
                            <span>Become a Partner</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-semibold mb-6">Client Dashboard</h1>

                    <div class="mb-8">
                        <h2 class="text-lg font-medium mb-4">Welcome back, {{ $user->name }}!</h2>
                        <p class="text-gray-600">From here you can manage your bike rentals, view your rental history, and find new bikes to rent.</p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 hover-lift fade-in" style="animation-delay: 0.1s; background: linear-gradient(135deg, #f0f9ff 0%, #e6f3ff 100%);">
                            <h3 class="text-sm font-medium text-blue-800 mb-2">Active Rentals</h3>
                            <p class="text-3xl font-bold text-blue-600 fade-in" style="animation-delay: 0.4s;">{{ $activeRentals }}</p>
                            <a href="{{ route('rentals.index', ['status' => 'active']) }}" class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-800 transition-all duration-200 hover:pl-1">View All →</a>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 hover-lift fade-in" style="animation-delay: 0.2s; background: linear-gradient(135deg, #fff3e6 0%, #ffe6cc 100%);">
                            <h3 class="text-sm font-medium text-yellow-800 mb-2">Pending Requests</h3>
                            <p class="text-3xl font-bold text-yellow-600 fade-in" style="animation-delay: 0.4s;">{{ $pendingRentals }}</p>
                            <a href="{{ route('rentals.index', ['status' => 'pending']) }}" class="inline-block mt-2 text-sm text-yellow-600 hover:text-yellow-800">View All →</a>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-100 hover-lift fade-in" style="animation-delay: 0.3s; background: linear-gradient(135deg, #e6f9e6 0%, #d9f2d9 100%);">
                            <h3 class="text-sm font-medium text-green-800 mb-2">Completed Rentals</h3>
                            <p class="text-3xl font-bold text-green-600 fade-in" style="animation-delay: 0.4s;">{{ $completedRentals }}</p>
                            <a href="{{ route('rentals.index', ['status' => 'completed']) }}" class="inline-block mt-2 text-sm text-green-600 hover:text-green-800">View All →</a>
                        </div>
                    </div>

                    <!-- Upcoming Rentals -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium">Upcoming Rentals</h2>
                            <a href="{{ route('rentals.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                        </div>

                        @if($upcomingRentals->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($upcomingRentals as $rental)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                        <div class="h-24 w-24 bg-gray-200 rounded-md overflow-hidden">
                                            @if($rental->bike->primaryImage)
                                                <img src="{{ asset('storage/' . $rental->bike->primaryImage->image_path) }}" alt="{{ $rental->bike->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-gray-400">No image</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $rental->bike->title }}</h3>
                                            <p class="text-sm text-gray-500 mb-3">
                                                {{ $rental->start_date->format('M d') }} - {{ $rental->end_date->format('M d, Y') }}
                                            </p>
                                            <div class="flex justify-between items-center">
                                                <div class="text-sm text-gray-700">
                                                    <span class="font-semibold">€{{ number_format($rental->total_price, 2) }}</span>
                                                </div>
                                                <a href="{{ route('rentals.show', $rental->id) }}" class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center fade-in" style="animation-delay: 0.3s; background: linear-gradient(135deg, #f5f7fa 0%, #f0f4f8 100%);">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4 pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No upcoming rentals</h3>
                            <p class="text-gray-600 mb-4">You don't have any rentals scheduled for the next 7 days.</p>
                            <a href="{{ route('search.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 hover:scale-105 transform">
                                Find Bikes to Rent
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Recent Rentals -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium">Recent Rental Activity</h2>
                            <a href="{{ route('rentals.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                        </div>

                        @if($recentRentals->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bike</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($recentRentals as $rental)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="h-24 w-24 bg-gray-200 rounded-md overflow-hidden">
                                                            @if($rental->bike->primaryImage)
                                                                <img src="{{ asset('storage/' . $rental->bike->primaryImage->image_path) }}" alt="{{ $rental->bike->title }}" class="w-full h-full object-cover">
                                                            @else
                                                                <div class="w-full h-full flex items-center justify-center">
                                                                    <span class="text-gray-400">No image</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $rental->bike->title }}</div>
                                                            <div class="text-sm text-gray-500">{{ $rental->bike->category->name }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $rental->bike->owner->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $rental->bike->location }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $rental->start_date->format('M d, Y') }}</div>
                                                    <div class="text-sm text-gray-500">to {{ $rental->end_date->format('M d, Y') }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
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
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                                        {{ ucfirst($rental->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('rentals.show', $rental->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                                    @if($rental->status === 'completed' && !$rental->bikeRating()->exists())
                                                        <a href="{{ route('rentals.show', $rental->id) }}#rate" class="text-green-600 hover:text-green-900">Rate</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <p class="text-gray-600">You haven't rented any bikes yet.</p>
                                <a href="{{ route('search.index') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Find Bikes to Rent
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" x-data="{ hoveredCard: null }">
                        <div class="bg-gray-50 p-6 rounded-lg hover-lift slide-in" style="animation-delay: 0.1s;" 
                            x-on:mouseenter="hoveredCard = 1" x-on:mouseleave="hoveredCard = null"
                            :class="{ 'bg-blue-50': hoveredCard === 1 }" 
                            style="transition: all 0.3s ease">
                            <h3 class="text-lg font-medium mb-3" :class="{ 'text-blue-700': hoveredCard === 1 }">Find Bikes</h3>
                            <p class="text-gray-600 mb-4">Browse our selection of bikes for rent in your area.</p>
                            <a href="{{ route('search.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                Browse Bikes
                            </a>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg hover-lift slide-in" style="animation-delay: 0.2s;" 
                            x-on:mouseenter="hoveredCard = 2" x-on:mouseleave="hoveredCard = null"
                            :class="{ 'bg-blue-50': hoveredCard === 2 }" 
                            style="transition: all 0.3s ease">
                            <h3 class="text-lg font-medium mb-3" :class="{ 'text-blue-700': hoveredCard === 2 }">Manage Rentals</h3>
                            <p class="text-gray-600 mb-4">View and manage all your bike rentals.</p>
                            <a href="{{ route('rentals.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 ">
                                View Rentals
                            </a>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg hover-lift slide-in" style="animation-delay: 0.3s;" 
                            x-on:mouseenter="hoveredCard = 3" x-on:mouseleave="hoveredCard = null"
                            :class="{ 'bg-blue-50': hoveredCard === 3 }" 
                            style="transition: all 0.3s ease">
                            <h3 class="text-lg font-medium mb-3" :class="{ 'text-blue-700': hoveredCard === 3 }">Manage Your Profile</h3>
                            <p class="text-gray-600 mb-4">Update your personal information and account settings.</p>
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Wait for the page to load
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips if not using Alpine
                const tooltips = document.querySelectorAll('.tooltip');
                tooltips.forEach(tooltip => {
                    tooltip.addEventListener('mouseenter', () => {
                        const tooltipText = tooltip.querySelector('.tooltip-text');
                        if (tooltipText) {
                            tooltipText.style.visibility = 'visible';
                            tooltipText.style.opacity = '1';
                        }
                    });
                    
                    tooltip.addEventListener('mouseleave', () => {
                        const tooltipText = tooltip.querySelector('.tooltip-text');
                        if (tooltipText) {
                            tooltipText.style.visibility = 'hidden';
                            tooltipText.style.opacity = '0';
                        }
                    });
                });
                
                // Add intersection observer for scroll animations
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('fade-in');
                            entry.target.style.opacity = '1';
                        }
                    });
                }, { threshold: 0.1 });
                
                document.querySelectorAll('.animate-on-scroll').forEach(el => {
                    el.style.opacity = '0';
                    observer.observe(el);
                });
            });
        </script>
    </div>
</x-app-layout>
