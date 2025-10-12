<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(auth()->check())
                        <h3 class="text-lg font-medium mb-4">Welcome, {{ Auth::user()->name }}!</h3>

                        @if(auth()->user()->hasRole('client'))
                            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                                <h4 class="text-blue-800 font-medium mb-2">Client Dashboard</h4>
                                <p class="text-blue-700">Browse our selection of bikes for rent or view your current and past rentals.</p>
                                <div class="mt-4 flex space-x-4">
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Browse Bikes</a>
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">My Rentals</a>
                                </div>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('partner'))
                            <div class="bg-green-50 p-4 rounded-lg mb-6">
                                <h4 class="text-green-800 font-medium mb-2">Partner Dashboard</h4>
                                <p class="text-green-700">Manage your bikes for rent or view and respond to rental requests.</p>
                                <div class="mt-4 flex space-x-4">
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Manage Bikes</a>
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Rental Requests</a>
                                </div>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('agent'))
                            <div class="bg-purple-50 p-4 rounded-lg mb-6">
                                <h4 class="text-purple-800 font-medium mb-2">Agent Dashboard</h4>
                                <p class="text-purple-700">Manage partners and oversee rentals in the system.</p>
                                <div class="mt-4 flex space-x-4">
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">Manage Partners</a>
                                    <a href="#" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">View Rentals</a>
                                </div>
                            </div>
                        @endif

                        @if(auth()->user()->hasRole('admin'))
                            <div class="bg-red-50 p-4 rounded-lg mb-6">
                                <h4 class="text-red-800 font-medium mb-2">Admin Dashboard</h4>
                                <p class="text-red-700">Manage all aspects of the Vélocité platform.</p>
                                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <a href="{{ route('admin.users') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Users</a>
                                    <a href="{{ route('admin.bikes') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Bikes</a>
                                    <a href="{{ route('admin.statistics') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Statistics</a>
                                    <a href="{{ route('admin.reports') }}" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">Reports</a>
                                </div>
                            </div>
                        @endif

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-gray-800 font-medium mb-2">Recent Activity</h4>
                            <p class="text-gray-600">No recent activity to display.</p>
                        </div>
                    @else
                        <p>Welcome to Vélocité! Please log in or register to get started.</p>
                        <div class="mt-4 flex space-x-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">Login</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Register</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
