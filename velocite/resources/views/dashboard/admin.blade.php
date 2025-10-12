<x-app-layout>
    <style>
        .fade-in { animation: fadeIn 0.6s ease-in-out; }
        .hover-lift { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 15px rgba(0,0,0,0.1); }
        .stat-card { transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 12px rgba(0,0,0,0.1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
        .tooltip { position: relative; }
        .tooltip-text { visibility: hidden; width: 120px; background-color: #333; color: #fff; text-align: center; border-radius: 6px; padding: 5px; position: absolute; z-index: 1; bottom: 125%; left: 50%; margin-left: -60px; opacity: 0; transition: opacity 0.3s; }
        .tooltip:hover .tooltip-text { visibility: visible; opacity: 1; }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-semibold mb-6">Admin Dashboard</h1>

                    <div class="mb-8">
                        <h2 class="text-lg font-medium mb-4">Welcome back, {{ $user->name }}!</h2>
                        <p class="text-gray-600">From here you can manage the entire Vélocité platform, including users, bikes, rentals, and system settings.</p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 stat-card hover-lift fade-in" style="animation-delay: 0.1s;">
                            <h3 class="text-sm font-medium text-blue-800 mb-2">Total Users</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalUsers }}</p>
                            <div class="flex justify-between mt-2">
                                <span class="text-xs text-blue-600">Clients: {{ $usersByRole['client'] }}</span>
                                <span class="text-xs text-blue-600">Partners: {{ $usersByRole['partner'] }}</span>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-100 stat-card hover-lift fade-in" style="animation-delay: 0.2s;">
                            <h3 class="text-sm font-medium text-green-800 mb-2">Total Bikes</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $totalBikes }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 stat-card hover-lift fade-in" style="animation-delay: 0.3s;">
                            <h3 class="text-sm font-medium text-yellow-800 mb-2">Total Rentals</h3>
                            <p class="text-3xl font-bold text-yellow-600">{{ $totalRentals }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100 stat-card hover-lift fade-in" style="animation-delay: 0.4s;">
                            <h3 class="text-sm font-medium text-purple-800 mb-2">Categories</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ $totalCategories }}</p>
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="mb-8" x-data="{ open: true }">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium">Recent Users</h2>
                            <div class="flex items-center">
                                <button @click="open = !open" class="mr-3 text-gray-500 hover:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="{'rotate-180': !open}" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="transition: transform 0.3s ease">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Users</a>
                            </div>
                        </div>

                        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($recentUsers as $user)
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 font-bold">
                                                        {{ substr($user->name, 0, 1) }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                @php
                                                    $roleColor = [
                                                        'client' => 'bg-blue-100 text-blue-800',
                                                        'partner' => 'bg-green-100 text-green-800',
                                                        'agent' => 'bg-purple-100 text-purple-800',
                                                        'admin' => 'bg-red-100 text-red-800',
                                                    ][$user->role] ?? 'bg-gray-100 text-gray-800';
                                                @endphp
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleColor }}">
                                                    {{ ucfirst($user->role) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $user->profile->city ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $user->created_at->format('M d, Y') }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Rentals -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium">Recent Rentals</h2>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Rentals</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bike</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Renter</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($recentRentals as $rental)
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">#{{ $rental->id }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $rental->bike->title }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $rental->renter->name }}</div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $rental->bike->owner->name }}</div>
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
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $rental->start_date->format('M d') }} - {{ $rental->end_date->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">€{{ number_format($rental->total_price, 2) }}</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Admin Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium mb-3">User Management</h3>
                            <p class="text-gray-600 mb-4">Create, edit and manage users across all roles.</p>
                            <div class="space-y-2">
                            <a href="{{ route('admin.users.create') }}" class="tooltip block w-full text-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                Create New User
                                <span class="tooltip-text">Add a new user to the system</span>
                            </a>
                                <a href="{{ route('admin.users') }}" class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Manage Users
                                </a>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium mb-3">Bike Management</h3>
                            <p class="text-gray-600 mb-4">Manage bikes, categories, and listings.</p>
                            <div class="space-y-2">
                                <a href="{{ route('admin.bikes') }}" class="block w-full text-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Manage Bikes
                                </a>
                                <a href="{{ route('admin.categories') }}" class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Manage Categories
                                </a>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium mb-3">System Analytics</h3>
                            <p class="text-gray-600 mb-4">View detailed statistics and reports.</p>
                            <div class="space-y-2">
                                <a href="{{ route('admin.statistics') }}" class="block w-full text-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                    View Statistics
                                </a>
                                <a href="{{ route('admin.reports') }}" class="block w-full text-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
                                    View Reports
                                </a>
                            </div>
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
