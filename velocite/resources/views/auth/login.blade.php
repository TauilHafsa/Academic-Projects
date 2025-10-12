<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">Log in to Vélocité</h1>
        <p class="mt-2 text-sm text-gray-600">Access your account to rent or manage bikes</p>
    </div>

    <!-- Role Selection Tabs -->
    <div class="flex mb-6 border-b border-gray-200">
        <button type="button" class="px-4 py-2 font-medium text-sm text-blue-600 border-b-2 border-blue-600" id="client-tab">
            Client
        </button>
        <button type="button" class="px-4 py-2 font-medium text-sm text-gray-500 hover:text-gray-700" id="partner-tab">
            Partner
        </button>

        <button type="button" class="px-4 py-2 font-medium text-sm text-gray-500 hover:text-gray-700" id="admin-tab">
            Admin
        </button>
    </div>

    <!-- Role Description -->
    <div class="mb-6 p-4 bg-blue-50 rounded-md border border-blue-100">
        <div id="role-description" class="text-sm text-blue-800">
            <p id="client-description" class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Client accounts can browse and rent bikes, manage reservations, and review rentals.
            </p>
            <p id="partner-description" class="hidden flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Partner accounts can list bikes for rent, manage listings, and handle reservations.
            </p>

            <p id="admin-description" class="hidden flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Admin accounts can manage all system aspects, users, and configurations.
            </p>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Hidden Role Input -->
        <input type="hidden" name="intended_role" id="intended_role" value="client">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:text-blue-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ml-3 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600 text-center">Don't have an account?</p>

        <div class="mt-4 grid grid-cols-1 gap-4">
            <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-4 py-2 bg-white border border-blue-300 rounded-md font-semibold text-xs text-blue-600 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Register as Client
            </a>
        </div>
    </div>

    <!-- Role Switching JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = ['client', 'partner', 'admin'];
            const roleInput = document.getElementById('intended_role');

            tabs.forEach(role => {
                const tab = document.getElementById(`${role}-tab`);

                tab.addEventListener('click', () => {
                    // Update active tab
                    tabs.forEach(r => {
                        const t = document.getElementById(`${r}-tab`);
                        t.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                        t.classList.add('text-gray-500');
                    });

                    tab.classList.remove('text-gray-500');
                    tab.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');

                    // Update role descriptions
                    tabs.forEach(r => {
                        const desc = document.getElementById(`${r}-description`);
                        desc.classList.add('hidden');
                    });

                    document.getElementById(`${role}-description`).classList.remove('hidden');

                    // Update hidden input
                    roleInput.value = role;
                });
            });
        });
    </script>
</x-guest-layout>
