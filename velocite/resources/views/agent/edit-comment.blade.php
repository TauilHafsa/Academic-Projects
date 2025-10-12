<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">Edit Comment</h1>
                            <p class="text-gray-600">
                                Rental #{{ $comment->rental_id }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('agent.moderate.comments') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Moderation
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 mb-6">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Comment Details</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                <span>{{ $comment->user->name }}</span>
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $comment->user_id === $comment->rental->renter_id ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $comment->user_id === $comment->rental->renter_id ? 'Renter' : 'Owner' }}
                                </span>
                                <span class="ml-2">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                            </p>
                        </div>

                        <form action="{{ route('agent.comment.update', $comment->id) }}" method="POST" class="p-6">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700">
                                                Please correct the following errors:
                                            </p>
                                            <ul class="mt-1 text-xs text-red-700 list-disc list-inside">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="original_content" class="block text-sm font-medium text-gray-700 mb-1">Original Content</label>
                                    <div class="bg-gray-50 rounded-lg p-4 text-gray-700">
                                        {{ $comment->content }}
                                    </div>
                                </div>

                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Edited Content</label>
                                    <textarea id="content" name="content" rows="6" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('content', $comment->content) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Edit the comment content to meet community guidelines.
                                    </p>
                                </div>

                                <div>
                                    <label for="moderation_notes" class="block text-sm font-medium text-gray-700 mb-1">Moderation Notes</label>
                                    <textarea id="moderation_notes" name="moderation_notes" rows="3"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Explain why this comment needed to be edited...">{{ old('moderation_notes', $comment->moderation_notes) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">
                                        These notes will be visible to the comment author.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <a href="{{ route('agent.moderate.comments') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Rental Information -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Rental Information</h3>
                                    <p class="mt-1 max-w-2xl text-sm text-gray-500">{{ $comment->rental->bike->title }}</p>
                                </div>
                                <a href="{{ route('agent.rental.show', $comment->rental_id) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    View Rental
                                </a>
                            </div>
                        </div>
                        <div class="border-b border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'confirmed' => 'bg-blue-100 text-blue-800',
                                                'ongoing' => 'bg-green-100 text-green-800',
                                                'completed' => 'bg-gray-100 text-gray-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                'rejected' => 'bg-red-100 text-red-800',
                                            ];
                                            $statusColor = $statusColors[$comment->rental->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst($comment->rental->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Renter</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $comment->rental->renter->name }} ({{ $comment->rental->renter->email }})
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Owner</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $comment->rental->bike->owner->name }} ({{ $comment->rental->bike->owner->email }})
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
