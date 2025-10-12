<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">Add Comment to Rental #{{ $rental->id }}</h1>
                            <p class="text-gray-600">
                                {{ $rental->bike->title }} ({{ $rental->bike->category->name }})
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('agent.rental.show', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back to Rental
                            </a>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 mb-6">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Communication Details</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Add a message to facilitate communication between the renter and owner.</p>
                        </div>

                        <form action="{{ route('agent.comment.store', $rental->id) }}" method="POST" class="p-6">
                            @csrf

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
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Message Content</label>
                                    <textarea id="content" name="content" rows="6" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Type your message here...">{{ old('content') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">
                                        This message will be visible to the selected recipients.
                                    </p>
                                </div>

                                <div>
                                    <label for="visible_to" class="block text-sm font-medium text-gray-700 mb-1">Message Visibility</label>
                                    <select id="visible_to" name="visible_to" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="both" {{ old('visible_to') === 'both' ? 'selected' : '' }}>Both renter and owner</option>
                                        <option value="client" {{ old('visible_to') === 'client' ? 'selected' : '' }}>Renter only</option>
                                        <option value="partner" {{ old('visible_to') === 'partner' ? 'selected' : '' }}>Owner only</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Select who should be able to see this message.
                                    </p>
                                </div>

                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="is_private" name="is_private" type="checkbox"
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                            {{ old('is_private') ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_private" class="font-medium text-gray-700">Mark as private note</label>
                                        <p class="text-gray-500">
                                            Private notes are only visible to support agents and administrators.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <a href="{{ route('agent.rental.show', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Add Comment
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Rental Summary -->
                    <div class="bg-gray-50 rounded-lg border border-gray-200">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Rental Summary</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Quick overview of the rental details.</p>
                        </div>
                        <div class="border-b border-gray-200">
                            <dl>
                                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Rental Period</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $rental->start_date->format('M d, Y') }} - {{ $rental->end_date->format('M d, Y') }}
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
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
                                            $statusColor = $statusColors[$rental->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst($rental->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-gray-50 px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Renter</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $rental->renter->name }} ({{ $rental->renter->email }})
                                    </dd>
                                </div>
                                <div class="bg-white px-4 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">Owner</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                        {{ $rental->bike->owner->name }} ({{ $rental->bike->owner->email }})
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
