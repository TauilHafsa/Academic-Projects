<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Bike') }}
            </h2>
            <a href="{{ route('partner.bikes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                {{ __('Back to Listings') }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('partner.bikes.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-6">
                                    <x-input-label for="title" :value="__('Listing Title')" />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="category_id" :value="__('Category')" />
                                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select a category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="location" :value="__('Location')" />
                                    <select id="location" name="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" x-data="citySelector()" x-on:change="updateCoordinates()">
                                        <option value="">Select a city</option>
                                        <option value="Casablanca" {{ old('location') == 'Casablanca' ? 'selected' : '' }}>Casablanca</option>
                                        <option value="Rabat" {{ old('location') == 'Rabat' ? 'selected' : '' }}>Rabat</option>
                                        <option value="Marrakech" {{ old('location') == 'Marrakech' ? 'selected' : '' }}>Marrakech</option>
                                        <option value="Fes" {{ old('location') == 'Fes' ? 'selected' : '' }}>Fes</option>
                                        <option value="Tangier" {{ old('location') == 'Tangier' ? 'selected' : '' }}>Tangier</option>
                                        <option value="Agadir" {{ old('location') == 'Agadir' ? 'selected' : '' }}>Agadir</option>
                                        <option value="Meknes" {{ old('location') == 'Meknes' ? 'selected' : '' }}>Meknes</option>
                                        <option value="Oujda" {{ old('location') == 'Oujda' ? 'selected' : '' }}>Oujda</option>
                                        <option value="Kenitra" {{ old('location') == 'Kenitra' ? 'selected' : '' }}>Kenitra</option>
                                        <option value="Tetouan" {{ old('location') == 'Tetouan' ? 'selected' : '' }}>Tetouan</option>
                                        <option value="Safi" {{ old('location') == 'Safi' ? 'selected' : '' }}>Safi</option>
                                        <option value="Mohammedia" {{ old('location') == 'Mohammedia' ? 'selected' : '' }}>Mohammedia</option>
                                        <option value="El Jadida" {{ old('location') == 'El Jadida' ? 'selected' : '' }}>El Jadida</option>
                                        <option value="Beni Mellal" {{ old('location') == 'Beni Mellal' ? 'selected' : '' }}>Beni Mellal</option>
                                        <option value="Nador" {{ old('location') == 'Nador' ? 'selected' : '' }}>Nador</option>
                                        <option value="Essaouira" {{ old('location') == 'Essaouira' ? 'selected' : '' }}>Essaouira</option>
                                        <option value="Chefchaouen" {{ old('location') == 'Chefchaouen' ? 'selected' : '' }}>Chefchaouen</option>
                                        <option value="Ouarzazate" {{ old('location') == 'Ouarzazate' ? 'selected' : '' }}>Ouarzazate</option>
                                        <option value="Ifrane" {{ old('location') == 'Ifrane' ? 'selected' : '' }}>Ifrane</option>
                                        <option value="Laayoune" {{ old('location') == 'Laayoune' ? 'selected' : '' }}>Laayoune</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('location')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-6">
                                    <x-input-label for="description" :value="__('Description')" />
                                    <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
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
                                    <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" :value="old('brand')" required />
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="model" :value="__('Model')" />
                                    <x-text-input id="model" class="block mt-1 w-full" type="text" name="model" :value="old('model')" required />
                                    <x-input-error :messages="$errors->get('model')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="year" :value="__('Year')" />
                                    <x-text-input id="year" class="block mt-1 w-full" type="number" name="year" :value="old('year', date('Y'))" min="1900" max="{{ date('Y') + 1 }}" required />
                                    <x-input-error :messages="$errors->get('year')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="color" :value="__('Color')" />
                                    <x-text-input id="color" class="block mt-1 w-full" type="text" name="color" :value="old('color')" required />
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="frame_size" :value="__('Frame Size')" />
                                    <x-text-input id="frame_size" class="block mt-1 w-full" type="text" name="frame_size" :value="old('frame_size')" placeholder="e.g., M, 54cm" />
                                    <x-input-error :messages="$errors->get('frame_size')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="condition" :value="__('Condition')" />
                                    <select id="condition" name="condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                                        <option value="like_new" {{ old('condition') == 'like_new' ? 'selected' : '' }}>Like New</option>
                                        <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                                        <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="is_electric" name="is_electric" type="checkbox" value="1" {{ old('is_electric') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="is_electric" class="font-medium text-gray-700">Electric Bike</label>
                                            <p class="text-gray-500">Check this if the bike has an electric motor</p>
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
                                        <x-text-input id="hourly_rate" class="block mt-1 w-full pl-7" type="number" name="hourly_rate" :value="old('hourly_rate')" step="0.01" min="1" max="1000" required />
                                    </div>
                                    <x-input-error :messages="$errors->get('hourly_rate')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="daily_rate" :value="__('Daily Rate (€)')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <x-text-input id="daily_rate" class="block mt-1 w-full pl-7" type="number" name="daily_rate" :value="old('daily_rate')" step="0.01" min="5" max="10000" required />
                                    </div>
                                    <x-input-error :messages="$errors->get('daily_rate')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-input-label for="weekly_rate" :value="__('Weekly Rate (€)')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <x-text-input id="weekly_rate" class="block mt-1 w-full pl-7" type="number" name="weekly_rate" :value="old('weekly_rate')" step="0.01" min="20" max="50000" />
                                    </div>
                                    <x-input-error :messages="$errors->get('weekly_rate')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="security_deposit" :value="__('Security Deposit (€)')" />
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">€</span>
                                        </div>
                                        <x-text-input id="security_deposit" class="block mt-1 w-full pl-7" type="number" name="security_deposit" :value="old('security_deposit')" step="0.01" min="0" max="10000" />
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Optional. This amount will be held as a deposit.</p>
                                    <x-input-error :messages="$errors->get('security_deposit')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="pt-6">
                            <h2 class="text-lg font-medium text-gray-900">Bike Images</h2>
                            <p class="mt-1 text-sm text-gray-500">Upload up to 5 images of your bike. The primary image will be the main image displayed in listings.</p>

                            <div class="mt-4" x-data="imageUploader()">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images</label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Upload images</span>
                                                    <input id="images" name="images[]" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg" multiple x-on:change="handleImageChange" required>
                                                </label>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview and Primary Selection -->
                                <div class="mt-4" x-show="previewUrls.length > 0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Image Preview & Primary Selection</label>
                                    <p class="text-sm text-gray-500 mb-2">Select one image as the primary image for your listing.</p>

                                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                        <template x-for="(url, index) in previewUrls" :key="index">
                                            <div class="relative border rounded-md overflow-hidden" :class="{ 'ring-2 ring-blue-500': primaryImage === index }">
                                                <img :src="url" class="h-40 w-full object-cover">
                                                <div class="absolute top-2 right-2">
                                                    <input type="radio" :id="'primary_' + index" name="primary_image" :value="index" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500" :checked="primaryImage === index" x-on:change="primaryImage = index">
                                                </div>
                                                <div class="absolute bottom-0 left-0 right-0 bg-gray-800 bg-opacity-50 text-white text-xs px-2 py-1">
                                                    Image <span x-text="index + 1"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <x-input-error :messages="$errors->get('images')" class="mt-2" />
                                <x-input-error :messages="$errors->get('primary_image')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Location Coordinates (Auto-filled) -->
                        <div class="pt-6" x-data="{ showCoordinates: false }">
                            <div class="flex items-center space-x-2">
                                <h2 class="text-lg font-medium text-gray-900">Location Coordinates</h2>
                                <button type="button" x-on:click="showCoordinates = !showCoordinates" class="text-sm text-blue-600 hover:text-blue-500 focus:outline-none focus:underline">
                                    <span x-show="!showCoordinates">Show coordinates</span>
                                    <span x-show="showCoordinates">Hide coordinates</span>
                                </button>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">These coordinates are automatically filled based on your selected city.</p>

                            <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6" x-show="showCoordinates">
                                <div class="sm:col-span-3">
                                    <x-input-label for="latitude" :value="__('Latitude')" />
                                    <x-text-input id="latitude" class="block mt-1 w-full bg-gray-100" type="text" name="latitude" :value="old('latitude')" readonly />
                                    <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                                </div>

                                <div class="sm:col-span-3">
                                    <x-input-label for="longitude" :value="__('Longitude')" />
                                    <x-text-input id="longitude" class="block mt-1 w-full bg-gray-100" type="text" name="longitude" :value="old('longitude')" readonly />
                                    <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end">
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Create Listing
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js for handling image preview and city selection -->
    <script>
        function imageUploader() {
            return {
                previewUrls: [],
                primaryImage: 0,
                handleImageChange(event) {
                    const files = event.target.files;

                    if (files.length > 5) {
                        alert('You can only upload a maximum of 5 images.');
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

        function citySelector() {
            return {
                cities: {
                    'Casablanca': { lat: 33.5731, lng: -7.5898 },
                    'Rabat': { lat: 34.0209, lng: -6.8416 },
                    'Marrakech': { lat: 31.6295, lng: -7.9811 },
                    'Fes': { lat: 34.0181, lng: -5.0078 },
                    'Tangier': { lat: 35.7673, lng: -5.7990 },
                    'Agadir': { lat: 30.4278, lng: -9.5981 },
                    'Meknes': { lat: 33.8969, lng: -5.5548 },
                    'Oujda': { lat: 34.6805, lng: -1.9063 },
                    'Kenitra': { lat: 34.2610, lng: -6.5802 },
                    'Tetouan': { lat: 35.5727, lng: -5.3695 },
                    'Safi': { lat: 32.2994, lng: -9.2372 },
                    'Mohammedia': { lat: 33.6866, lng: -7.3830 },
                    'El Jadida': { lat: 33.2549, lng: -8.5039 },
                    'Beni Mellal': { lat: 32.3371, lng: -6.3498 },
                    'Nador': { lat: 35.1681, lng: -2.9330 },
                    'Essaouira': { lat: 31.5085, lng: -9.7595 },
                    'Chefchaouen': { lat: 35.1715, lng: -5.2714 },
                    'Ouarzazate': { lat: 30.9187, lng: -6.9117 },
                    'Ifrane': { lat: 33.5201, lng: -5.1054 },
                    'Laayoune': { lat: 27.1536, lng: -13.2033 }
                },
                updateCoordinates() {
                    const location = document.getElementById('location').value;
                    if (location && this.cities[location]) {
                        document.getElementById('latitude').value = this.cities[location].lat;
                        document.getElementById('longitude').value = this.cities[location].lng;
                    } else {
                        document.getElementById('latitude').value = '';
                        document.getElementById('longitude').value = '';
                    }
                },
                init() {
                    // Set initial values if location is already selected (e.g. on page reload with validation errors)
                    const initialLocation = document.getElementById('location').value;
                    if (initialLocation && this.cities[initialLocation]) {
                        document.getElementById('latitude').value = this.cities[initialLocation].lat;
                        document.getElementById('longitude').value = this.cities[initialLocation].lng;
                    }
                }
            };
        }
    </script>
</x-app-layout>
