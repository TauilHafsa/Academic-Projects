<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Register for Vélocité</h1>
        <p class="mt-2 text-sm text-gray-600">Create an account to start renting bikes</p>
    </div>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- CIN -->
        <div class="mt-4">
            <x-input-label for="cin" :value="__('CIN (Carte d\'Identité Nationale)')" />
            <x-text-input id="cin" class="block mt-1 w-full" type="text" name="cin" :value="old('cin')" required />
            <x-input-error :messages="$errors->get('cin')" class="mt-2" />
        </div>

        <!-- CIN Front Image -->
        <div class="mt-4">
            <x-input-label for="cin_front" :value="__('CIN Front Side (Image)')" />
            <input id="cin_front" class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" type="file" name="cin_front" accept="image/jpeg,image/png,image/jpg" required />
            <p class="mt-1 text-xs text-gray-500">Upload JPEG or PNG image (max 2MB)</p>
            <x-input-error :messages="$errors->get('cin_front')" class="mt-2" />
        </div>

        <!-- CIN Back Image -->
        <div class="mt-4">
            <x-input-label for="cin_back" :value="__('CIN Back Side (Image)')" />
            <input id="cin_back" class="block mt-1 w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" type="file" name="cin_back" accept="image/jpeg,image/png,image/jpg" required />
            <p class="mt-1 text-xs text-gray-500">Upload JPEG or PNG image (max 2MB)</p>
            <x-input-error :messages="$errors->get('cin_back')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600 text-center">
            Want to list your bikes for rent? Register now and you can become a partner later!
        </p>
    </div>
</x-guest-layout>