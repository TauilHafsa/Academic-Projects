<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">System Statistics</h1>
                            <p class="text-gray-600">Overview of platform metrics and analytics</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <a href="{{ route('admin.reports') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                View Reports
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Main Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-blue-800 text-sm font-medium">Total Users</p>
                                    <p class="text-blue-600 text-3xl font-bold mt-1">{{ $totalUsers }}</p>
                                    <p class="text-blue-600 text-sm mt-1">{{ $newUsersThisMonth }} new this month</p>
                                </div>
                                <div class="bg-blue-100 rounded-full p-3">
                                    <svg class="w-6 h-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-green-800 text-sm font-medium">Total Bikes</p>
                                    <p class="text-green-600 text-3xl font-bold mt-1">{{ $totalBikes }}</p>
                                    <p class="text-green-600 text-sm mt-1">{{ $availableBikes }} currently available</p>
                                </div>
                                <div class="bg-green-100 rounded-full p-3">
                                    <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-100">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-yellow-800 text-sm font-medium">Total Rentals</p>
                                    <p class="text-yellow-600 text-3xl font-bold mt-1">{{ $totalRentals }}</p>
                                    <p class="text-yellow-600 text-sm mt-1">{{ $activeRentals }} active now</p>
                                </div>
                                <div class="bg-yellow-100 rounded-full p-3">
                                    <svg class="w-6 h-6 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-purple-800 text-sm font-medium">Comments</p>
                                    <p class="text-purple-600 text-3xl font-bold mt-1">{{ $totalComments }}</p>
                                    <p class="text-purple-600 text-sm mt-1">{{ $pendingModeration }} pending moderation</p>
                                </div>
                                <div class="bg-purple-100 rounded-full p-3">
                                    <svg class="w-6 h-6 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- User Distribution -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">User Distribution</h3>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2 mb-2">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-4">
                                                @php
                                                    $clientPercent = $totalUsers > 0 ? ($usersByRole['client'] / $totalUsers) * 100 : 0;
                                                    $partnerPercent = $totalUsers > 0 ? ($usersByRole['partner'] / $totalUsers) * 100 : 0;
                                                    $agentPercent = $totalUsers > 0 ? ($usersByRole['agent'] / $totalUsers) * 100 : 0;
                                                    $adminPercent = $totalUsers > 0 ? ($usersByRole['admin'] / $totalUsers) * 100 : 0;
                                                @endphp
                                                <div class="bg-blue-500 h-4 rounded-l-full" style="width: {{ $clientPercent }}%"></div>
                                                <div class="bg-green-500 h-4" style="width: {{ $partnerPercent }}%"></div>
                                                <div class="bg-purple-500 h-4" style="width: {{ $agentPercent }}%"></div>
                                                <div class="bg-red-500 h-4 rounded-r-full" style="width: {{ $adminPercent }}%"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between mt-1 text-xs text-gray-500">
                                            <span>Clients</span>
                                            <span>Partners</span>
                                            <span>Agents</span>
                                            <span>Admins</span>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                                <span class="text-sm font-medium text-gray-700">Clients</span>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $usersByRole['client'] }} ({{ round($clientPercent) }}%)</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                                <span class="text-sm font-medium text-gray-700">Partners</span>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $usersByRole['partner'] }} ({{ round($partnerPercent) }}%)</span>
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                                <span class="text-sm font-medium text-gray-700">Agents</span>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $usersByRole['agent'] }} ({{ round($agentPercent) }}%)</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                                <span class="text-sm font-medium text-gray-700">Admins</span>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $usersByRole['admin'] }} ({{ round($adminPercent) }}%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Rentals Chart -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Monthly Rentals ({{ date('Y') }})</h3>
                            </div>
                            <div class="p-4">
                                <div class="h-60">
                                    <div class="flex flex-col h-full">
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            @php
                                                $maxRental = max($rentalData);
                                            @endphp
                                            <span>{{ $maxRental }} rentals</span>
                                            <span>0 rentals</span>
                                        </div>
                                        <div class="flex justify-between h-full items-end">
                                            @foreach($rentalData as $month => $count)
                                                <div class="flex flex-col items-center">
                                                    @php
                                                        $height = $maxRental > 0 ? ($count / $maxRental) * 100 : 0;
                                                    @endphp
                                                    <div class="w-6 bg-blue-200 rounded-t" style="height: {{ $height }}%">
                                                        <div class="w-full h-full bg-blue-500 bg-opacity-60 rounded-t"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500 mt-1">{{ date('M', mktime(0, 0, 0, $month, 1)) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- More Stats Sections -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Bikes by Category -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Bikes by Category</h3>
                            </div>
                            <div class="p-4">
                                <div class="space-y-4">
                                    @foreach($bikesByCategory as $category)
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="font-medium text-gray-700">{{ $category->name }}</span>
                                                <span class="text-gray-700">{{ $category->bikes_count }} bikes</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    $percent = $totalBikes > 0 ? ($category->bikes_count / $totalBikes) * 100 : 0;
                                                @endphp
                                                <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">{{ round($percent) }}% of total bikes</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Rental Status Overview -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Rental Status Overview</h3>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-green-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-green-800 mb-1">Completed</h4>
                                        <p class="text-2xl font-bold text-green-600">{{ $completedRentals }}</p>
                                        <p class="text-xs text-green-600 mt-1">
                                            {{ $totalRentals > 0 ? round(($completedRentals / $totalRentals) * 100) : 0 }}% of total
                                        </p>
                                    </div>
                                    <div class="bg-blue-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-blue-800 mb-1">Active</h4>
                                        <p class="text-2xl font-bold text-blue-600">{{ $activeRentals }}</p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            {{ $totalRentals > 0 ? round(($activeRentals / $totalRentals) * 100) : 0 }}% of total
                                        </p>
                                    </div>
                                    <div class="bg-yellow-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-yellow-800 mb-1">Pending Evaluations</h4>
                                        <p class="text-2xl font-bold text-yellow-600">{{ $pendingEvaluations }}</p>
                                        <p class="text-xs text-yellow-600 mt-1">
                                            {{ $totalEvaluations > 0 ? round(($pendingEvaluations / $totalEvaluations) * 100) : 0 }}% of evaluations
                                        </p>
                                    </div>
                                    <div class="bg-red-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-red-800 mb-1">Cancelled</h4>
                                        <p class="text-2xl font-bold text-red-600">{{ $cancelledRentals }}</p>
                                        <p class="text-xs text-red-600 mt-1">
                                            {{ $totalRentals > 0 ? round(($cancelledRentals / $totalRentals) * 100) : 0 }}% of total
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
