<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Upgrade to Premium') }}: {{ $bike->title }}
            </h2>
            <a href="{{ route('partner.bikes.show', $bike) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                {{ __('Cancel') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Premium Listing Benefits</h3>
                    <p class="text-gray-600 mb-6">Upgrade your bike listing to premium status to gain more visibility and attract more renters. Premium listings receive:</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <h4 class="text-md font-medium text-gray-900 mb-1">Increased Visibility</h4>
                            <p class="text-sm text-gray-600">Your listing appears at the top of search results and category pages.</p>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <h4 class="text-md font-medium text-gray-900 mb-1">Special Badge</h4>
                            <p class="text-sm text-gray-600">A premium badge shows potential renters that your listing is high-quality.</p>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            <h4 class="text-md font-medium text-gray-900 mb-1">Performance Analytics</h4>
                            <p class="text-sm text-gray-600">Access detailed metrics on how your listing is performing.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ selectedType: 'featured', selectedDuration: '7' }">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Choose Your Premium Plan</h3>

                    <form method="POST" action="{{ route('partner.bikes.store-premium', $bike) }}" class="space-y-6">
                        @csrf

                        <!-- Premium Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Premium Type</label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative bg-white border rounded-lg overflow-hidden transition-all duration-200"
                                    :class="{ 'border-purple-500 ring-2 ring-purple-500': selectedType === 'featured', 'border-gray-300': selectedType !== 'featured' }"
                                    x-on:click="selectedType = 'featured'">

                                    <input type="radio" name="type" value="featured" class="sr-only" x-model="selectedType">

                                    <div class="p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-1 flex justify-between">
                                            <span>Featured</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak x-show="selectedType === 'featured'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-2">Basic premium visibility</p>
                                        <div class="text-purple-600 font-semibold">
                                            <div x-cloak x-show="selectedDuration === '7'">€9.99</div>
                                            <div x-cloak x-show="selectedDuration === '14'">€17.99</div>
                                            <div x-cloak x-show="selectedDuration === '30'">€29.99</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative bg-white border rounded-lg overflow-hidden transition-all duration-200"
                                    :class="{ 'border-purple-500 ring-2 ring-purple-500': selectedType === 'spotlight', 'border-gray-300': selectedType !== 'spotlight' }"
                                    x-on:click="selectedType = 'spotlight'">

                                    <input type="radio" name="type" value="spotlight" class="sr-only" x-model="selectedType">

                                    <div class="p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-1 flex justify-between">
                                            <span>Spotlight</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak x-show="selectedType === 'spotlight'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-2">Enhanced visibility + homepage feature</p>
                                        <div class="text-purple-600 font-semibold">
                                            <div x-cloak x-show="selectedDuration === '7'">€14.99</div>
                                            <div x-cloak x-show="selectedDuration === '14'">€27.99</div>
                                            <div x-cloak x-show="selectedDuration === '30'">€49.99</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative bg-white border rounded-lg overflow-hidden transition-all duration-200"
                                    :class="{ 'border-purple-500 ring-2 ring-purple-500': selectedType === 'promoted', 'border-gray-300': selectedType !== 'promoted' }"
                                    x-on:click="selectedType = 'promoted'">

                                    <input type="radio" name="type" value="promoted" class="sr-only" x-model="selectedType">

                                    <div class="p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-1 flex justify-between">
                                            <span>Promoted</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak x-show="selectedType === 'promoted'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </h4>
                                        <p class="text-sm text-gray-600 mb-2">Maximum visibility + featured everywhere</p>
                                        <div class="text-purple-600 font-semibold">
                                            <div x-cloak x-show="selectedDuration === '7'">€19.99</div>
                                            <div x-cloak x-show="selectedDuration === '14'">€34.99</div>
                                            <div x-cloak x-show="selectedDuration === '30'">€69.99</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Duration Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Duration</label>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="relative bg-white border rounded-lg overflow-hidden transition-all duration-200"
                                    :class="{ 'border-purple-500 ring-2 ring-purple-500': selectedDuration === '7', 'border-gray-300': selectedDuration !== '7' }"
                                    x-on:click="selectedDuration = '7'">

                                    <input type="radio" name="duration" value="7" class="sr-only" x-model="selectedDuration">

                                    <div class="p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-1 flex justify-between">
                                            <span>7 Days</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak x-show="selectedDuration === '7'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </h4>
                                        <p class="text-sm text-gray-600">1 week premium visibility</p>
                                    </div>
                                </div>

                                <div class="relative bg-white border rounded-lg overflow-hidden transition-all duration-200"
                                    :class="{ 'border-purple-500 ring-2 ring-purple-500': selectedDuration === '14', 'border-gray-300': selectedDuration !== '14' }"
                                    x-on:click="selectedDuration = '14'">

                                    <input type="radio" name="duration" value="14" class="sr-only" x-model="selectedDuration">

                                    <div class="p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-1 flex justify-between">
                                            <span>14 Days</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak x-show="selectedDuration === '14'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </h4>
                                        <p class="text-sm text-gray-600">2 weeks premium visibility</p>
                                    </div>
                                </div>

                                <div class="relative bg-white border rounded-lg overflow-hidden transition-all duration-200"
                                    :class="{ 'border-purple-500 ring-2 ring-purple-500': selectedDuration === '30', 'border-gray-300': selectedDuration !== '30' }"
                                    x-on:click="selectedDuration = '30'">

                                    <input type="radio" name="duration" value="30" class="sr-only" x-model="selectedDuration">

                                    <div class="p-4">
                                        <h4 class="text-md font-medium text-gray-900 mb-1 flex justify-between">
                                            <span>30 Days</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak x-show="selectedDuration === '30'">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </h4>
                                        <p class="text-sm text-gray-600">1 month premium visibility</p>
                                    </div>
                                </div>
                            </div>

                            <x-input-error :messages="$errors->get('duration')" class="mt-2" />
                        </div>

                        <!-- Summary -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="text-md font-medium text-gray-900 mb-3">Summary</h4>

                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Plan:</span>
                                <span class="font-medium" x-text="selectedType.charAt(0).toUpperCase() + selectedType.slice(1)"></span>
                            </div>

                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Duration:</span>
                                <span class="font-medium" x-text="selectedDuration + ' days'"></span>
                            </div>

                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Start Date:</span>
                                <span class="font-medium">{{ now()->format('M d, Y') }}</span>
                            </div>

                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">End Date:</span>
                                <span class="font-medium">
                                    <span x-show="selectedDuration === '7'">{{ now()->addDays(7)->format('M d, Y') }}</span>
                                    <span x-show="selectedDuration === '14'">{{ now()->addDays(14)->format('M d, Y') }}</span>
                                    <span x-show="selectedDuration === '30'">{{ now()->addDays(30)->format('M d, Y') }}</span>
                                </span>
                            </div>

                            <div class="flex justify-between pt-3 mt-3 border-t border-gray-200">
                                <span class="text-gray-900 font-medium">Total:</span>
                                <span class="text-purple-600 font-bold text-lg">
                                    <span x-show="selectedType === 'featured' && selectedDuration === '7'">€9.99</span>
                                    <span x-show="selectedType === 'featured' && selectedDuration === '14'">€17.99</span>
                                    <span x-show="selectedType === 'featured' && selectedDuration === '30'">€29.99</span>

                                    <span x-show="selectedType === 'spotlight' && selectedDuration === '7'">€14.99</span>
                                    <span x-show="selectedType === 'spotlight' && selectedDuration === '14'">€27.99</span>
                                    <span x-show="selectedType === 'spotlight' && selectedDuration === '30'">€49.99</span>

                                    <span x-show="selectedType === 'promoted' && selectedDuration === '7'">€19.99</span>
                                    <span x-show="selectedType === 'promoted' && selectedDuration === '14'">€34.99</span>
                                    <span x-show="selectedType === 'promoted' && selectedDuration === '30'">€69.99</span>
                                </span>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                Upgrade Listing
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
