<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-semibold mb-6">Agent Dashboard</h1>

                    <div class="mb-8">
                        <h2 class="text-lg font-medium mb-4">Welcome back, {{ $user->name }}!</h2>
                        <p class="text-gray-600">
                            From here you can manage bikes, users, and rentals
                            @if($city)
                                in {{ $city }}.
                            @else
                                across all locations.
                            @endif
                        </p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                            <h3 class="text-sm font-medium text-blue-800 mb-2">Total Users</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $totalUsers }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                            <h3 class="text-sm font-medium text-green-800 mb-2">Total Bikes</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $totalBikes }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                            <h3 class="text-sm font-medium text-purple-800 mb-2">Total Rentals</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ $totalRentals }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                            <h3 class="text-sm font-medium text-yellow-800 mb-2">Pending Comments</h3>
                            <p class="text-3xl font-bold text-yellow-600">{{ $pendingComments }}</p>
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium">Recent Users</h2>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Users</a>
                        </div>

                        @if($users->count() > 0)
                            <div class="overflow-x-auto">
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
                                        @foreach($users as $user)
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
                                                    <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                                    <a href="#" class="text-green-600 hover:text-green-900">Contact</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <p class="text-gray-600">No users found in your area.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Bikes -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium">Recent Bikes</h2>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All Bikes</a>
                        </div>

                        @if($bikes->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($bikes->take(6) as $bike)
                                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                                        <div class="h-40 bg-gray-200 relative">
                                            @if($bike->primaryImage)
                                                <img src="{{ asset('storage/bikes/placeholder.jpg') }}"
                                                    alt="{{ $bike->title }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-gray-400">No image available</span>
                                                </div>
                                            @endif

                                            @if(!$bike->is_available)
                                                <div class="absolute top-0 right-0 bg-red-600 text-white py-1 px-3 text-xs font-semibold">
                                                    Not Available
                                                </div>
                                            @endif
                                        </div>
                                        <div class="p-4">
                                            <div class="flex justify-between items-start">
                                                <h3 class="text-base font-semibold text-gray-900">{{ $bike->title }}</h3>
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $bike->category->name }}</span>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Owner: {{ $bike->owner->name }}
                                            </div>
                                            <div class="mt-3 flex justify-between items-center">
                                                <div class="text-blue-600 font-semibold">€{{ number_format($bike->daily_rate, 2) }} <span class="text-xs text-gray-500">/ day</span></div>
                                                <a href="{{ route('bikes.show', $bike->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <p class="text-gray-600">No bikes found in your area.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium mb-3">Manage Your Profile</h3>
                            <p class="text-gray-600 mb-4">Update your personal information and account settings.</p>
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Edit Profile
                            </a>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium mb-3">Rental Management</h3>
                            <p class="text-gray-600 mb-4">Handle issues, manage communications, and resolve disputes.</p>
                            <a href="{{ route('agent.rentals') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Manage Rentals
                            </a>
                        </div>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-medium mb-3">Comment Moderation</h3>
                            <p class="text-gray-600 mb-4">Review and moderate comments between renters and bike owners.</p>
                            <a href="{{ route('agent.moderate.comments') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Moderate Comments
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
