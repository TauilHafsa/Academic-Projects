<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Become a Partner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Partner Terms and Conditions</h3>
                    
                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200 mb-6 max-h-96 overflow-y-auto">
                        <h4 class="font-bold mb-2">1. Partner Eligibility and Responsibilities</h4>
                        <p class="mb-4">By becoming a Partner on Vélocité, you agree to maintain high standards of bike quality, safety, and customer service. You must ensure all bikes are roadworthy, clean, and properly maintained before each rental.</p>
                        
                        <h4 class="font-bold mb-2">2. Commission Structure</h4>
                        <p class="mb-4">Vélocité takes a 15% commission on each successful rental. Payments are processed within 7 business days after the rental is completed and the bike is confirmed returned in good condition.</p>
                        
                        <h4 class="font-bold mb-2">3. Listing Requirements</h4>
                        <p class="mb-4">All bikes must include clear photos, accurate descriptions, and proper specification details. Misrepresentation of bikes may result in removal from the platform.</p>
                        
                        <h4 class="font-bold mb-2">4. Cancellation Policy</h4>
                        <p class="mb-4">Partners must honor all confirmed bookings. Cancellations by partners less than 48 hours before rental start may incur penalties including compensation to the affected renter.</p>
                        
                        <h4 class="font-bold mb-2">5. Insurance and Liability</h4>
                        <p class="mb-4">Partners are strongly advised to maintain appropriate insurance for their bikes. Vélocité provides basic coverage for theft and damage during rental periods, subject to a deductible and our insurance terms.</p>
                        
                        <h4 class="font-bold mb-2">6. Dispute Resolution</h4>
                        <p class="mb-4">In case of disputes with renters, Vélocité will mediate fairly based on evidence from both parties. Partners agree to abide by Vélocité's final decision in disputes.</p>
                        
                        <h4 class="font-bold mb-2">7. Account Termination</h4>
                        <p class="mb-4">Vélocité reserves the right to terminate Partner accounts for repeated policy violations, consistently low ratings, or fraudulent activity.</p>
                        
                        <h4 class="font-bold mb-2">8. Tax Responsibilities</h4>
                        <p class="mb-4">Partners are responsible for reporting income earned through Vélocité to relevant tax authorities and complying with all applicable tax laws.</p>
                        
                        <h4 class="font-bold mb-2">9. Platform Updates</h4>
                        <p class="mb-4">These terms may be updated periodically. Partners will be notified of significant changes, and continued use of the platform constitutes acceptance of updated terms.</p>
                        
                        <h4 class="font-bold mb-2">10. Data Privacy</h4>
                        <p>Partners agree to handle renter information in accordance with data privacy laws and Vélocité's privacy policy.</p>
                    </div>
                    
                    <form method="POST" action="{{ route('become.partner.accept') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Phone Number -->
                        <div>
                            <x-input-label for="phone_number" :value="__('Phone Number')" />
                            <x-text-input id="phone_number" class="block mt-1 w-full" type="text" name="phone_number" :value="old('phone_number', $user->profile->phone_number ?? '')" required autofocus />
                            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                        </div>
                        
                        <!-- City Dropdown -->
                        <div>
                            <x-input-label for="city" :value="__('City')" />
                            <select id="city" name="city" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                <option value="">Select a city</option>
                                <option value="Agadir" {{ old('city', $user->profile->city ?? '') == 'Agadir' ? 'selected' : '' }}>Agadir</option>
                                <option value="Casablanca" {{ old('city', $user->profile->city ?? '') == 'Casablanca' ? 'selected' : '' }}>Casablanca</option>
                                <option value="Fez" {{ old('city', $user->profile->city ?? '') == 'Fez' ? 'selected' : '' }}>Fez</option>
                                <option value="Marrakech" {{ old('city', $user->profile->city ?? '') == 'Marrakech' ? 'selected' : '' }}>Marrakech</option>
                                <option value="Meknes" {{ old('city', $user->profile->city ?? '') == 'Meknes' ? 'selected' : '' }}>Meknes</option>
                                <option value="Oujda" {{ old('city', $user->profile->city ?? '') == 'Oujda' ? 'selected' : '' }}>Oujda</option>
                                <option value="Rabat" {{ old('city', $user->profile->city ?? '') == 'Rabat' ? 'selected' : '' }}>Rabat</option>
                                <option value="Salé" {{ old('city', $user->profile->city ?? '') == 'Salé' ? 'selected' : '' }}>Salé</option>
                                <option value="Tangier" {{ old('city', $user->profile->city ?? '') == 'Tangier' ? 'selected' : '' }}>Tangier</option>
                                <option value="Tetouan" {{ old('city', $user->profile->city ?? '') == 'Tetouan' ? 'selected' : '' }}>Tetouan</option>
                                <option value="El Jadida" {{ old('city', $user->profile->city ?? '') == 'El Jadida' ? 'selected' : '' }}>El Jadida</option>
                                <option value="Kenitra" {{ old('city', $user->profile->city ?? '') == 'Kenitra' ? 'selected' : '' }}>Kenitra</option>
                                <option value="Mohammedia" {{ old('city', $user->profile->city ?? '') == 'Mohammedia' ? 'selected' : '' }}>Mohammedia</option>
                                <option value="Nador" {{ old('city', $user->profile->city ?? '') == 'Nador' ? 'selected' : '' }}>Nador</option>
                                <option value="Essaouira" {{ old('city', $user->profile->city ?? '') == 'Essaouira' ? 'selected' : '' }}>Essaouira</option>
                                <option value="Taza" {{ old('city', $user->profile->city ?? '') == 'Taza' ? 'selected' : '' }}>Taza</option>
                                <option value="Larache" {{ old('city', $user->profile->city ?? '') == 'Larache' ? 'selected' : '' }}>Larache</option>
                                <option value="Safi" {{ old('city', $user->profile->city ?? '') == 'Safi' ? 'selected' : '' }}>Safi</option>
                                <option value="Ouarzazate" {{ old('city', $user->profile->city ?? '') == 'Ouarzazate' ? 'selected' : '' }}>Ouarzazate</option>
                                <option value="Ifrane" {{ old('city', $user->profile->city ?? '') == 'Ifrane' ? 'selected' : '' }}>Ifrane</option>
                            </select>
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>
                        
                        <!-- Address -->
                        <div>
                            <x-input-label for="address" :value="__('Address')" />
                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $user->profile->address ?? '')" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                        
                        <!-- Bio -->
                        <div>
                            <x-input-label for="bio" :value="__('Bio (Tell renters about yourself)')" />
                            <textarea id="bio" name="bio" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" rows="3">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                        </div>
                        
                        <!-- Profile Picture -->
                        <div>
                            <x-input-label for="profile_picture" :value="__('Profile Picture')" />
                            <input id="profile_picture" name="profile_picture" type="file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*" />
                            <p class="mt-1 text-xs text-gray-500">Optional. Max file size: 2MB. Recommended: Square image, at least 300x300px.</p>
                            <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                        </div>
                        
                        <!-- Terms Acceptance -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="accept_terms" name="accept_terms" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" value="1">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="accept_terms" class="font-medium text-gray-700">I have read and agree to the Partner Terms and Conditions</label>
                                <x-input-error :messages="$errors->get('accept_terms')" class="mt-2" />
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('client.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            
                            <x-primary-button class="ml-4">
                                {{ __('Accept and Become a Partner') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>