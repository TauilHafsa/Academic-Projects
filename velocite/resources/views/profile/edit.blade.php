<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profile Picture') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your profile picture.") }}
                            </p>
                        </header>

                        <div class="mt-6 flex items-center gap-6">
                            <!-- Current Profile Picture -->
                            <div>
                                @if($user->profile && $user->profile->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile->profile_picture) }}"
                                        alt="{{ $user->name }}"
                                        class="h-24 w-24 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 text-2xl font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Upload Form -->
                            <form method="post" action="{{ route('profile.picture.update') }}" class="flex-1" enctype="multipart/form-data">
                                @csrf

                                <div>
                                    <x-input-label for="profile_picture" :value="__('Upload new picture')" />
                                    <input id="profile_picture" name="profile_picture" type="file"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        accept="image/*" required>
                                    <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
                                    <p class="mt-1 text-xs text-gray-500">Max file size: 2MB. Best size: 300x300px.</p>
                                </div>

                                <div class="mt-4">
                                    <x-primary-button>{{ __('Update Picture') }}</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('CIN Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Your CIN details and images. You can update the images but not the CIN number.") }}
                            </p>
                        </header>

                        <!-- CIN Number Display -->
                        <div class="mt-6">
                            <x-input-label for="cin" :value="__('CIN Number')" />
                            <x-text-input id="cin" name="cin" type="text" class="mt-1 block w-full bg-gray-100" 
                                value="{{ $user->cin }}" disabled readonly />
                            <p class="mt-1 text-xs text-gray-500">CIN number cannot be changed.</p>
                        </div>

                        <!-- CIN Images -->
                        <div class="mt-6">
                            <form method="post" action="{{ route('profile.cin.update') }}" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Front CIN Image -->
                                <div class="mb-6">
                                    <x-input-label :value="__('CIN Front Image')" />
                                    <div class="mt-2 mb-4">
                                        @if($user->cin_front)
                                            <img src="{{ $user->getCinFrontUrlAttribute() }}" 
                                                alt="CIN Front" 
                                                class="max-h-48 border border-gray-200 rounded">
                                        @else
                                            <div class="p-4 bg-gray-100 text-gray-500 text-center rounded">
                                                No image available
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <x-input-label for="cin_front" :value="__('Update CIN Front')" />
                                    <input id="cin_front" name="cin_front" type="file"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        accept="image/jpeg,image/png,image/jpg">
                                    <x-input-error class="mt-2" :messages="$errors->get('cin_front')" />
                                </div>
                                
                                <!-- Back CIN Image -->
                                <div class="mb-6">
                                    <x-input-label :value="__('CIN Back Image')" />
                                    <div class="mt-2 mb-4">
                                        @if($user->cin_back)
                                            <img src="{{ $user->getCinBackUrlAttribute() }}" 
                                                alt="CIN Back" 
                                                class="max-h-48 border border-gray-200 rounded">
                                        @else
                                            <div class="p-4 bg-gray-100 text-gray-500 text-center rounded">
                                                No image available
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <x-input-label for="cin_back" :value="__('Update CIN Back')" />
                                    <input id="cin_back" name="cin_back" type="file"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        accept="image/jpeg,image/png,image/jpg">
                                    <x-input-error class="mt-2" :messages="$errors->get('cin_back')" />
                                </div>
                                
                                <div class="flex items-center gap-4">
                                    <x-primary-button>{{ __('Update CIN Images') }}</x-primary-button>

                                    @if (session('status') === 'cin-images-updated')
                                        <p
                                            x-data="{ show: true }"
                                            x-show="show"
                                            x-transition
                                            x-init="setTimeout(() => show = false, 2000)"
                                            class="text-sm text-gray-600"
                                        >{{ __('Images updated.') }}</p>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profile Information') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your account's profile information and email address.") }}
                            </p>
                        </header>

                        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                            @csrf
                        </form>

                        <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div>
                                        <p class="text-sm mt-2 text-gray-800">
                                            {{ __('Your email address is unverified.') }}

                                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ __('Click here to re-send the verification email.') }}
                                            </button>
                                        </p>

                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-sm text-green-600">
                                                {{ __('A new verification link has been sent to your email address.') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div>
                                <x-input-label for="phone_number" :value="__('Phone Number')" />
                                <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 block w-full" :value="old('phone_number', $user->profile ? $user->profile->phone_number : '')" autocomplete="tel" />
                                <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                            </div>

                            <div>
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->profile ? $user->profile->city : '')" required autocomplete="address-level2" />
                                <x-input-error class="mt-2" :messages="$errors->get('city')" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Address')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->profile ? $user->profile->address : '')" autocomplete="street-address" />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div>
                                <x-input-label for="bio" :value="__('Bio')" />
                                <textarea id="bio" name="bio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="3">{{ old('bio', $user->profile ? $user->profile->bio : '') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Saved.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
