<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-blue-600">
                        Vélocité
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    @auth
                        <!-- Common links for all authenticated users -->
                        @if(auth()->user()->hasRole('client'))
                            <x-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                                {{ __('Browse Bikes') }}
                            </x-nav-link>
                        @elseif(auth()->user()->hasRole('partner'))
                            <x-nav-link :href="route('partner.dashboard')" :active="request()->routeIs('partner.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('bikes.index')">
                                {{ __('My Bikes') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('rental-requests.index')">
                                {{ __('Rental Requests') }}
                            </x-nav-link>
                        @elseif(auth()->user()->hasRole('agent'))
                            <x-nav-link :href="route('agent.dashboard')" :active="request()->routeIs('agent.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('agent.bikes')">
                                {{ __('Bikes') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('agent.users')">
                                {{ __('Users') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('agent.rentals')">
                                {{ __('Rentals') }}
                            </x-nav-link>
                        @elseif(auth()->user()->hasRole('admin'))
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('admin.users')">
                                {{ __('Users') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('admin.bikes')">
                                {{ __('Bikes') }}
                            </x-nav-link>
                            <x-nav-link href="#" :active="request()->routeIs('admin.settings')">
                                {{ __('Settings') }}
                            </x-nav-link>
                        @endif
                    @else
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                            {{ __('Home') }}
                        </x-nav-link>
                        <x-nav-link href="#bikes" :active="false">
                            {{ __('Bikes') }}
                        </x-nav-link>
                        <x-nav-link href="#" :active="false">
                            {{ __('About Us') }}
                        </x-nav-link>
                        <x-nav-link href="#" :active="false">
                            {{ __('Contact') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center">
                                    <div class="mr-2">
                                        @if(auth()->user()->profile && auth()->user()->profile->profile_picture)
                                            <img src="{{ asset('storage/' . auth()->user()->profile->profile_picture) }}"
                                                alt="{{ auth()->user()->name }}"
                                                class="h-8 w-8 rounded-full object-cover">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 font-bold">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Role Badge -->
                            <div class="px-4 py-2 text-xs text-gray-500 border-b border-gray-200">
                                @php
                                    $roleColors = [
                                        'client' => 'bg-blue-100 text-blue-800',
                                        'partner' => 'bg-green-100 text-green-800',
                                        'agent' => 'bg-purple-100 text-purple-800',
                                        'admin' => 'bg-red-100 text-red-800',
                                    ];
                                    $roleColor = $roleColors[Auth::user()->role] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="block mb-1">Logged in as:</span>
                                <span class="px-2 py-1 rounded-full text-xs {{ $roleColor }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Role specific links -->
                            @if(auth()->user()->hasRole('client'))
                                <x-dropdown-link href="#">
                                    {{ __('My Rentals') }}
                                </x-dropdown-link>
                            @elseif(auth()->user()->hasRole('partner'))
                                <x-dropdown-link href="#">
                                    {{ __('My Earnings') }}
                                </x-dropdown-link>
                            @elseif(auth()->user()->hasRole('agent') || auth()->user()->hasRole('admin'))
                                <x-dropdown-link href="#">
                                    {{ __('System Settings') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600">Log in</a>
                        <a href="{{ route('register.client') }}" class="px-4 py-2 bg-blue-600 text-white font-medium text-sm rounded-md hover:bg-blue-700">Register</a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <!-- Common links for all authenticated users -->
                @if(auth()->user()->hasRole('client'))
                    <x-responsive-nav-link :href="route('client.dashboard')" :active="request()->routeIs('client.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Browse Bikes') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->hasRole('partner'))
                    <x-responsive-nav-link :href="route('partner.dashboard')" :active="request()->routeIs('partner.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('bikes.index')">
                        {{ __('My Bikes') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('rental-requests.index')">
                        {{ __('Rental Requests') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->hasRole('agent'))
                    <x-responsive-nav-link :href="route('agent.dashboard')" :active="request()->routeIs('agent.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('agent.bikes')">
                        {{ __('Bikes') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('agent.users')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('agent.rentals')">
                        {{ __('Rentals') }}
                    </x-responsive-nav-link>
                @elseif(auth()->user()->hasRole('admin'))
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('admin.users')">
                        {{ __('Users') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('admin.bikes')">
                        {{ __('Bikes') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="#" :active="request()->routeIs('admin.settings')">
                        {{ __('Settings') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    {{ __('Home') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#bikes" :active="false">
                    {{ __('Bikes') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#" :active="false">
                    {{ __('About Us') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="#" :active="false">
                    {{ __('Contact') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        @auth
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4 flex items-center">
                    <div class="flex-shrink-0 mr-3">
                        @if(auth()->user()->profile && auth()->user()->profile->profile_picture)
                            <img src="{{ asset('storage/' . auth()->user()->profile->profile_picture) }}"
                                alt="{{ auth()->user()->name }}"
                                class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center text-blue-600 font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Role specific links -->
                    @if(auth()->user()->hasRole('client'))
                        <x-responsive-nav-link href="#">
                            {{ __('My Rentals') }}
                        </x-responsive-nav-link>
                    @elseif(auth()->user()->hasRole('partner'))
                        <x-responsive-nav-link href="#">
                            {{ __('My Earnings') }}
                        </x-responsive-nav-link>
                    @elseif(auth()->user()->hasRole('agent') || auth()->user()->hasRole('admin'))
                        <x-responsive-nav-link href="#">
                            {{ __('System Settings') }}
                        </x-responsive-nav-link>
                    @endif

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="py-3 border-t border-gray-200">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register.client')">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>
