<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Bike Listing') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('partner.bikes.show', $bike) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('partner.bikes.update', $bike) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-6">
                                    <x-input-label for="title" :value="__('Listing Title')" />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $bike->title)" required autofocus />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="category_id" :value="__('Category')" />
                                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (old('category_id', $bike->category_id) == $category->id) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="location" :value="__('Location')" />
                                    <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $bike->location)" required />
                                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-6">
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $bike->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Bike Details -->
                        <div class="pt-6">
                            <h2 class="text-lg font-medium text-gray-900">Bike Details</h2>
                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <x-input-label for="brand" :value="__('Brand')" />
                                    <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand', $bike->brand)" required />
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="model" :value="__('Model')" />
                                    <x-text-input id="model" class="block mt-1 w-full" type="text" name="model" :value="old('model', $bike->model)" required />
                                    <x-input-error :messages="$errors->get('model')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="year" :value="__('Year')" />
                                    <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', $bike->year)" min="1900" max="{{ date('Y') + 1 }}" required />
                                    <x-input-error :messages="$errors->get('year')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="color" :value="__('Color')" />
                                    <x-text-input id="color" class="block mt-1 w-full" type="text" name="color" :value="old('color', $bike->color)" required />
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="frame_size" :value="__('Frame Size')" />
                                    <x-text-input id="frame_size" class="block mt-1 w-full" type="text" name="frame_size" :value="old('frame_size', $bike->frame_size)" placeholder="e.g., M, 54cm" />
                                    <x-input-error :messages="$errors->get('frame_size')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="condition" :value="__('Condition')" />
                                    <select id="condition" name="condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="new" {{ old('condition', $bike->condition) == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="like_new" {{ old('condition', $bike->condition) == 'like_new' ? 'selected' : '' }}>Like New</option>
                                        <option value="good" {{ old('condition', $bike->condition) == 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ old('condition', $bike->condition) == 'fair' ? 'selected' : '' }}>Fair</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_electric" name="is_electric" type="checkbox" value="1" {{ old('is_electric', $bike->is_electric) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_electric" class="font-medium text-gray-700">Electric Bike</label>
                                            <p class="text-gray-500">Check this if the bike has an electric motor</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:col-span-3">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_available" name="is_available" type="checkbox" value="1" {{ old('is_available', $bike->is_available) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_available" class="font-medium text-gray-700">Active Listing</label>
                                            <p class="text-gray-500">Uncheck to archive this listing temporarily</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="pt-6">
                            <h2 class="text-lg font-medium text-gray-900">Pricing</h2>
                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-2">
                                    <x-input-label for="hourly_rate" :value="__('Hourly Rate (€)')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <x-text-input id="hourly_rate" class="block mt-1 w-full pl-7" type="number" name="hourly_rate" :value="old('hourly_rate', $bike->hourly_rate)" step="0.01" min="1" max="1000" required />
                                    </div>
                                    <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="daily_rate" :value="__('Daily Rate (€)')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <x-text-input id="daily_rate" class="block mt-1 w-full pl-7" type="number" name="daily_rate" :value="old('daily_rate', $bike->daily_rate)" step="0.01" min="5" max="10000" required />
                                    </div>
                                    <x-input-error :messages="$errors->get('daily_rate')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="weekly_rate" :value="__('Weekly Rate (€)')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <x-text-input id="weekly_rate" class="block mt-1 w-full pl-7" type="number" name="weekly_rate" :value="old('weekly_rate', $bike->weekly_rate)" step="0.01" min="20" max="50000" />
                                    </div>
                                    <x-input-error :messages="$errors->get('weekly_rate')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="security_deposit" :value="__('Security Deposit (€)')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <x-text-input id="security_deposit" class="block mt-1 w-full pl-7" type="number" name="security_deposit" :value="old('security_deposit', $bike->security_deposit)" step="0.01" min="0" max="10000" />
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Optional. This amount will be held as a deposit.</p>
                                    <x-input-error :messages="$errors->get('security_deposit')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="pt-6" x-data="imageManager()">
                            <h2 class="text-lg font-medium text-gray-900">Bike Images</h2>
                            <p class="mt-1 text-sm text-gray-500">Manage the images of your bike. Keep at least one image for your listing.</p>

                            <!-- Current Images -->
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>

                                @if($bike->images->count() > 0)
                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                        @foreach($bike->images as $image)
                                            <div class="relative border rounded-md overflow-hidden">
                                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Bike image" class="h-40 w-full object-cover">

                                                <div class="absolute top-2 right-2 flex space-x-2">
                                                    <!-- Primary Image Selection -->
                                                    <input type="radio" id="primary_{{ $image->id }}" name="primary_image_id" value="{{ $image->id }}" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" {{ $image->is_primary ? 'checked' : '' }}>

                                                    <!-- Keep Image Checkbox -->
                                                    <input type="checkbox" id="keep_{{ $image->id }}" name="keep_images[]" value="{{ $image->id }}" class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500" checked>
                                                </div>

                                                <div class="absolute bottom-0 left-0 right-0 bg-gray-800 bg-opacity-50 text-white text-xs px-2 py-1 flex justify-between">
                                                    <span>{{ $image->is_primary ? 'Primary' : 'Image' }}</span>
                                                    <label for="keep_{{ $image->id }}" class="text-xs cursor-pointer hover:text-red-300">
                                                        <input type="checkbox" class="sr-only peer" checked>
                                                        <span class="hidden peer-checked:inline">Keep</span>
                                                        <span class="peer-checked:hidden">Remove</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-500">No images currently uploaded.</p>
                                @endif
                            </div>

                            <!-- Upload New Images -->
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Images</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="new_images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>Upload additional images</span>
                                                <input id="new_images" name="new_images[]" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg" multiple x-on:change="handleImageChange">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview New Images -->
                            <div class="mt-4" x-show="previewUrls.length > 0">
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Images Preview</label>

                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                    <template x-for="(url, index) in previewUrls" :key="index">
                                        <div class="relative border rounded-md overflow-hidden">
                                            <img :src="url" alt="New bike image" class="h-40 w-full object-cover">

                                            <div class="absolute top-2 right-2">
                                                <input type="radio" :id="'primary_new_' + index" name="primary_image_id" :value="'new_' + index" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            </div>

                                            <div class="absolute bottom-0 left-0 right-0 bg-gray-800 bg-opacity-50 text-white text-xs px-2 py-1">
                                                New Image <span x-text="index + 1"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <x-input-error :messages="$errors->get('new_images')" class="mt-2" />
                            <x-input-error :messages="$errors->get('primary_image_id')" class="mt-2" />
                        </div>

                        <!-- Location (Optional) -->
                        <div class="pt-6">
                            <h2 class="text-lg font-medium text-gray-900">Exact Location (Optional)</h2>
                            <p class="mt-1 text-sm text-gray-500">Providing exact coordinates helps clients find your bike more easily.</p>

                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <x-input-label for="latitude" :value="__('Latitude')" />
                                    <x-text-input id="latitude" class="block mt-1 w-full" type="text" name="latitude" :value="old('latitude', $bike->latitude)" placeholder="e.g., 48.8584" />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="longitude" :value="__('Longitude')" />
                                    <x-text-input id="longitude" class="block mt-1 w-full" type="text" name="longitude" :value="old('longitude', $bike->longitude)" placeholder="e.g., 2.2945" />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end">
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js for handling image preview -->
    <script>
        function imageManager() {
            return {
                previewUrls: [],
                handleImageChange(event) {
                    const files = event.target.files;

                    if (files.length > 5) {
                        alert('You can only upload a maximum of 5 new images.');
                        event.target.value = '';
                        this.previewUrls = [];
                        return;
                    }

                    this.previewUrls = [];
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        if (file.size > 5 * 1024 * 1024) {
                            alert('File size should not exceed 5MB');
                            event.target.value = '';
                            this.previewUrls = [];
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previewUrls.push(e.target.result);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            };
        }
    </script>
</x-app-layout>
