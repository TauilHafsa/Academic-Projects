<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">Rental #{{ $rental->id }} Details</h1>
                            <p class="text-gray-600">
                                Agent communication management
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('agent.rentals') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Rentals
                            </a>
                        </div>
                    </div>

                    <!-- Rental Information -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200 mb-6">
                        <div class="px-4 py-5 sm:px-6 flex justify-between items-center border-b border-gray-200">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Rental Information</h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $rental->bike->title }} ({{ $rental->bike->category->name }})</p>
                            </div>
                            <div>
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
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusColor }}">
                                    {{ ucfirst($rental->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="border-b border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Rental Period</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $rental->start_date->format('M d, Y') }} - {{ $rental->end_date->format('M d, Y') }}
                                        <span class="text-xs text-gray-500">({{ $rental->start_date->diffInDays($rental->end_date) + 1 }} days)</span>
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Bike Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $rental->bike->location }}</dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Renter</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $rental->renter->name }}
                                        <span class="text-gray-500">({{ $rental->renter->email }})</span>
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Bike Owner</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $rental->bike->owner->name }}
                                        <span class="text-gray-500">({{ $rental->bike->owner->email }})</span>
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Total Price</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">€{{ number_format($rental->total_price, 2) }}</dd>
                                </div>
                                @if($rental->status === 'cancelled' || $rental->status === 'rejected')
                                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                        <dt class="text-sm font-medium text-gray-500">Cancellation Reason</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                            {{ $rental->cancellation_reason ?? 'No reason provided' }}
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Agent Action Buttons -->
                    <div class="flex flex-wrap gap-4 mb-8">
                        <a href="{{ route('agent.comment.create', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            Add Comment
                        </a>
                        <a href="{{ route('agent.evaluation.create', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Send Evaluation Form
                        </a>
                    </div>

                    <!-- Communication History -->
                    <div class="bg-white rounded-lg border border-gray-200 mb-6">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Communication History</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">All messages exchanged between the renter, owner, and support staff.</p>
                        </div>

                        <div class="p-6">
                            @if($comments->count() > 0)
                                <div class="space-y-6">
                                    @foreach($comments as $comment)
                                        <div class="flex space-x-4 {{ $comment->user_id === $rental->renter_id ? 'justify-start' : 'justify-end' }}">
                                            <div class="max-w-lg {{ $comment->user_id === $rental->renter_id ? 'bg-blue-50 border-blue-200' : ($comment->user_id === $rental->bike->owner_id ? 'bg-green-50 border-green-200' : 'bg-purple-50 border-purple-200') }} p-4 rounded-lg border">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <span class="font-medium text-gray-900">
                                                            {{ $comment->user->name }}
                                                            @if($comment->user_id === $rental->renter_id)
                                                                <span class="text-blue-600 text-xs">(Renter)</span>
                                                            @elseif($comment->user_id === $rental->bike->owner_id)
                                                                <span class="text-green-600 text-xs">(Owner)</span>
                                                            @else
                                                                <span class="text-purple-600 text-xs">(Agent)</span>
                                                            @endif
                                                        </span>
                                                        <span class="text-xs text-gray-500 ml-2">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                                                    </div>
                                                    <div class="flex space-x-2">
                                                        @if($comment->is_private)
                                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Private</span>
                                                        @endif
                                                        @if($comment->agent_comment)
                                                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                                                Visible to: {{ ucfirst($comment->agent_comment_visibility) }}
                                                            </span>
                                                        @endif
                                                        @if($comment->is_moderated)
                                                            <span class="px-2 py-1 text-xs {{ $comment->moderation_status === 'approved' ? 'bg-green-100 text-green-800' : ($comment->moderation_status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }} rounded-full">
                                                                {{ ucfirst($comment->moderation_status) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-gray-700">
                                                    {{ $comment->content }}
                                                </div>

                                                @if(!$comment->is_moderated && $comment->user_id !== Auth::id())
                                                    <div class="mt-3 pt-3 border-t border-gray-200 flex justify-end">
                                                        <form action="{{ route('agent.comment.approve', $comment->id) }}" method="POST" class="inline mr-2">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                                Approve
                                                            </button>
                                                        </form>
                                                        <a href="{{ route('agent.comment.edit', $comment->id) }}" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                                                            Edit
                                                        </a>
                                                        <button type="button" onclick="showRejectModal('{{ $comment->id }}')" class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                            Reject
                                                        </button>
                                                    </div>
                                                @endif

                                                @if($comment->moderation_status === 'rejected' && $comment->moderation_notes)
                                                    <div class="mt-2 text-sm text-red-600 bg-red-50 p-2 rounded">
                                                        <p class="font-semibold">Rejection reason:</p>
                                                        <p>{{ $comment->moderation_notes }}</p>
                                                    </div>
                                                @endif

                                                @if($comment->moderation_status === 'edited' && $comment->moderation_notes)
                                                    <div class="mt-2 text-sm text-blue-600 bg-blue-50 p-2 rounded">
                                                        <p class="font-semibold">Edited by agent:</p>
                                                        <p>{{ $comment->moderation_notes }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No messages yet</h3>
                                    <p class="mt-1 text-sm text-gray-500">Get started by adding a comment to facilitate communication.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('agent.comment.create', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Add first comment
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ratings Information -->
                    <div class="bg-white rounded-lg border border-gray-200 mb-6">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Ratings Information</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Ratings provided by users for this rental.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                            <div class="border rounded-lg p-4 {{ $rental->bikeRating ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                                <h4 class="font-medium text-gray-900 mb-2">Bike Rating</h4>
                                @if($rental->bikeRating)
                                    <div class="flex items-center mb-1">
                                        <span class="font-medium text-lg mr-2">{{ $rental->bikeRating->rating }}</span>
                                        <span class="text-sm text-gray-500">out of 5</span>
                                    </div>
                                    @if($rental->bikeRating->review)
                                        <p class="text-gray-700 text-sm mt-2">"{{ $rental->bikeRating->review }}"</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-2">Rated by {{ $rental->renter->name }} on {{ $rental->bikeRating->created_at->format('M d, Y') }}</p>
                                @else
                                    <p class="text-gray-500 text-sm">No bike rating provided yet.</p>
                                @endif
                            </div>

                            <div class="border rounded-lg p-4 {{ $rental->userRatings->count() > 0 ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                <h4 class="font-medium text-gray-900 mb-2">User Rating</h4>
                                @if($rental->userRatings->count() > 0)
                                    @foreach($rental->userRatings as $userRating)
                                        <div class="flex items-center mb-1">
                                            <span class="font-medium text-lg mr-2">{{ $userRating->rating }}</span>
                                            <span class="text-sm text-gray-500">out of 5</span>
                                        </div>
                                        @if($userRating->review)
                                            <p class="text-gray-700 text-sm mt-2">"{{ $userRating->review }}"</p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-2">
                                            {{ $userRating->rater_id === $rental->renter_id ?
                                                'Client rated Partner' :
                                                'Partner rated Client' }} on {{ $userRating->created_at->format('M d, Y') }}
                                        </p>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm">No user ratings provided yet.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Comment Modal -->
    <div id="reject-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Reject Comment</h3>
                <button type="button" onclick="hideRejectModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="reject-form" action="" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for rejection</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" required></textarea>
                    <p class="mt-1 text-xs text-gray-500">Please explain why this comment is being rejected. This reason will be visible to the author.</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideRejectModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Reject Comment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(commentId) {
            document.getElementById('reject-form').action = "{{ route('agent.comment.reject', '') }}/" + commentId;
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>
