<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">System Reports</h1>
                            <p class="text-gray-600">Detailed analytics and performance reports</p>
                        </div>
                        <div class="mt-4 md:mt-0 flex space-x-3">
                            <a href="{{ route('admin.statistics') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                View Statistics
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Back to Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Revenue Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-green-800 text-sm font-medium">Total Revenue</p>
                                    <p class="text-green-600 text-3xl font-bold mt-1">€{{ number_format($totalRevenue, 2) }}</p>
                                    <p class="text-green-600 text-sm mt-1">From completed rentals</p>
                                </div>
                                <div class="bg-green-100 rounded-full p-3">
                                    <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 p-6 rounded-lg border border-green-100">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-green-800 text-sm font-medium">Revenue This Month</p>
                                    <p class="text-green-600 text-3xl font-bold mt-1">€{{ number_format($revenueThisMonth, 2) }}</p>
                                    <p class="text-green-600 text-sm mt-1">{{ date('F Y') }}</p>
                                </div>
                                <div class="bg-green-100 rounded-full p-3">
                                    <svg class="w-6 h-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Details -->
                    <div class="grid grid-cols-1 gap-6 mb-8">
                        <!-- Monthly Revenue Chart -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Monthly Revenue ({{ date('Y') }})</h3>
                            </div>
                            <div class="p-4">
                                <div class="h-72">
                                    <div class="flex flex-col h-full">
                                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                                            @php
                                                $maxRevenue = max($revenueData);
                                            @endphp
                                            <span>€{{ number_format($maxRevenue, 2) }}</span>
                                            <span>€0.00</span>
                                        </div>
                                        <div class="flex justify-between h-full items-end">
                                            @foreach($revenueData as $month => $revenue)
                                                <div class="flex flex-col items-center">
                                                    @php
                                                        $height = $maxRevenue > 0 ? ($revenue / $maxRevenue) * 100 : 0;
                                                    @endphp
                                                    <div class="w-6 md:w-10 bg-green-200 rounded-t" style="height: {{ $height }}%">
                                                        <div class="w-full h-full bg-green-500 bg-opacity-60 rounded-t"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500 mt-1">{{ date('M', mktime(0, 0, 0, $month, 1)) }}</span>
                                                    <span class="text-xs text-gray-700 mt-1">€{{ number_format($revenue, 0) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Users and Bikes -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Top Renters -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Top Renters</h3>
                            </div>
                            <div class="p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rentals</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($topRenters as $renter)
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 font-bold">
                                                                {{ substr($renter->name, 0, 1) }}
                                                            </div>
                                                            <div class="ml-3">
                                                                <div class="text-sm font-medium text-gray-900">{{ $renter->name }}</div>
                                                                <div class="text-xs text-gray-500">{{ $renter->email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ $renter->rentals_count }} rentals
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $renter->profile->city ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        No rental data available.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Top Partners -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Top Partners</h3>
                            </div>
                            <div class="p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Partner</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rentals</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($topPartners as $partner)
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="h-8 w-8 rounded-full bg-green-200 flex items-center justify-center text-green-600 font-bold">
                                                                {{ substr($partner->name, 0, 1) }}
                                                            </div>
                                                            <div class="ml-3">
                                                                <div class="text-sm font-medium text-gray-900">{{ $partner->name }}</div>
                                                                <div class="text-xs text-gray-500">{{ $partner->email }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ $partner->received_rentals_count }} rentals
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $partner->profile->city ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        No partner data available.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- More Reports Sections -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Top Performing Bikes -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Top Performing Bikes</h3>
                            </div>
                            <div class="p-4">
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bike</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rentals</th>
                                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($topBikes as $bike)
                                                <tr>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex-shrink-0">
                                                                @if($bike->primaryImage)
                                                                    <img src="{{ asset('storage/bikes/thumbs/' . $bike->primaryImage->filename) }}" alt="{{ $bike->title }}" class="h-8 w-8 rounded-full object-cover">
                                                                @else
                                                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                        </svg>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="ml-3">
                                                                <div class="text-sm font-medium text-gray-900">{{ $bike->title }}</div>
                                                                <div class="text-xs text-gray-500">{{ $bike->owner->name }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $bike->category->name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                            {{ $bike->rentals_count }} rentals
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                        €{{ number_format($bike->daily_rate, 2) }}/day
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                                        No bike performance data available.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Rentals by City -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="px-4 py-5 border-b border-gray-200">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Rentals by City</h3>
                            </div>
                            <div class="p-4">
                                <div class="space-y-4">
                                    @forelse($rentalsByCity as $index => $cityData)
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="font-medium text-gray-700">{{ $cityData->city }}</span>
                                                <span class="text-gray-700">{{ $cityData->count }} rentals</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                @php
                                                    $maxCityRentals = $rentalsByCity->max('count');
                                                    $percent = $maxCityRentals > 0 ? ($cityData->count / $maxCityRentals) * 100 : 0;
                                                    $colors = ['blue', 'green', 'purple', 'yellow', 'red', 'indigo', 'pink', 'gray'];
                                                    $color = $colors[$index % count($colors)];
                                                @endphp
                                                <div class="bg-{{ $color }}-500 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-500 text-center py-4">
                                            No city rental data available.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
