<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-semibold">Send Evaluation Form for Rental #{{ $rental->id }}</h1>
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
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Evaluation Form Details</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Create an evaluation form to collect feedback or resolve issues.</p>
                        </div>

                        <form action="{{ route('agent.evaluation.send', $rental->id) }}" method="POST" class="p-6">
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
                                    <label for="evaluation_type" class="block text-sm font-medium text-gray-700 mb-1">Evaluation Type</label>
                                    <select id="evaluation_type" name="evaluation_type" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="" disabled selected>Select an evaluation type...</option>
                                        <option value="service" {{ old('evaluation_type') === 'service' ? 'selected' : '' }}>Service Feedback</option>
                                        <option value="dispute" {{ old('evaluation_type') === 'dispute' ? 'selected' : '' }}>Dispute Resolution</option>
                                        <option value="damage" {{ old('evaluation_type') === 'damage' ? 'selected' : '' }}>Damage Assessment</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Select the type of evaluation to send.
                                    </p>
                                </div>

                                <div>
                                    <label for="recipient" class="block text-sm font-medium text-gray-700 mb-1">Send To</label>
                                    <select id="recipient" name="recipient" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        <option value="both" {{ old('recipient') === 'both' ? 'selected' : '' }}>Both renter and owner</option>
                                        <option value="client" {{ old('recipient') === 'client' ? 'selected' : '' }}>Renter only: {{ $rental->renter->name }}</option>
                                        <option value="partner" {{ old('recipient') === 'partner' ? 'selected' : '' }}>Owner only: {{ $rental->bike->owner->name }}</option>
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Select who should receive this evaluation form.
                                    </p>
                                </div>

                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message to Recipients</label>
                                    <textarea id="message" name="message" rows="6" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Explain why you are sending this evaluation form and what information you need...">{{ old('message') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">
                                        This message will be sent with the evaluation form request.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <a href="{{ route('agent.rental.show', $rental->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Send Evaluation Form
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Evaluation Type Information -->
                    <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                        <h3 class="text-lg font-medium text-blue-900 mb-3">About Evaluation Forms</h3>

                        <div class="space-y-4 text-sm text-blue-800">
                            <div>
                                <h4 class="font-semibold mb-1">Service Feedback</h4>
                                <p>Use this type to collect general feedback about the rental experience, customer service, or platform usability. This is helpful for improving our services.</p>
                            </div>

                            <div>
                                <h4 class="font-semibold mb-1">Dispute Resolution</h4>
                                <p>Use this type when there's a disagreement between the renter and bike owner that requires intervention. Both parties will be asked to provide their perspective on the issue.</p>
                            </div>

                            <div>
                                <h4 class="font-semibold mb-1">Damage Assessment</h4>
                                <p>Use this type when there's a claim of damage to the bike. Both parties will be asked to provide details and evidence about the condition of the bike before and after the rental.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
