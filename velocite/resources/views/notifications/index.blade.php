<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">All Notifications</h3>
                        <div class="flex space-x-4">
                            <form method="POST" action="{{ route('notifications.read.all') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Mark All as Read
                                </button>
                            </form>
                            <form method="POST" action="{{ route('notifications.clear.all') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Are you sure you want to clear all notifications?')">
                                    Clear All
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Notifications List -->
                    <div class="space-y-4">
                        @forelse ($notifications as $notification)
                            <div class="rounded-lg border {{ $notification->is_read ? 'border-gray-200 bg-white' : 'border-blue-200 bg-blue-50' }} overflow-hidden">
                                <div class="px-6 py-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 mb-1">{{ $notification->content }}</div>
                                            <div class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>

                                            <!-- Additional information based on notification type -->
                                            @if($notification->type === 'rental_status')
                                                <div class="mt-2 text-xs text-blue-600">
                                                    <a href="{{ $notification->link }}" class="hover:underline">View rental details</a>
                                                </div>
                                            @elseif($notification->type === 'new_comment')
                                                <div class="mt-2 text-xs text-blue-600">
                                                    <a href="{{ $notification->link }}" class="hover:underline">View comment</a>
                                                </div>
                                            @elseif($notification->type === 'new_rating')
                                                <div class="mt-2 text-xs text-blue-600">
                                                    <a href="{{ $notification->link }}" class="hover:underline">View rating details</a>
                                                </div>
                                            @elseif($notification->type === 'booking_confirmation')
                                                <div class="mt-2 text-xs text-blue-600">
                                                    <a href="{{ $notification->link }}" class="hover:underline">View booking details</a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex space-x-2">
                                            @if(!$notification->is_read)
                                                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                        Mark as read
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <p class="mt-2 text-sm font-medium">No notifications</p>
                                <p class="mt-1 text-xs">You don't have any notifications yet.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
