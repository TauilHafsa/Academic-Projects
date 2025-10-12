<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Book a Bike</h1>
            </div>
            <a href="{{ route('bikes.show', $bike->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to Bike
            </a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="md:grid md:grid-cols-3 md:gap-6">
                <div class="md:col-span-1 px-4 py-5 sm:px-6">
                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $bike->title }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ $bike->brand }} {{ $bike->model }} - {{ $bike->category->name }}</p>

                        <div class="w-full h-48 bg-gray-200 rounded-md relative overflow-hidden mb-4">
                            @if($bike->primaryImage)
                                <img src="{{ asset('storage/' . $bike->primaryImage->image_path) }}" alt="{{ $bike->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-gray-400">No image available</span>
                                </div>
                            @endif

                            @if($bike->is_electric)
                                <div class="absolute top-0 right-0 bg-green-600 text-white py-1 px-3 text-xs font-semibold">
                                    Electric
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">Daily Rate</h4>
                                <p class="text-lg font-semibold text-blue-600">€{{ number_format($bike->daily_rate, 2) }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-medium text-gray-500">Location</h4>
                                <p class="text-sm text-gray-800">{{ $bike->location }}</p>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-xs font-medium text-gray-500">Owner</h4>
                            <p class="text-sm text-gray-800">{{ $bike->owner->name }}</p>
                        </div>
                    </div>
                    
                    <!-- Available Date Ranges -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Available Date Ranges</h4>
                        <div class="space-y-2 mb-3">
                            @if(count($bike->getAvailableDateRanges()) > 0)
                                @foreach($bike->getAvailableDateRanges() as $range)
                                    <div class="text-sm py-2 px-3 bg-blue-50 rounded border border-blue-100 flex justify-between">
                                        <span>{{ \Carbon\Carbon::parse($range['start_date'])->format('d M Y') }}</span>
                                        <span class="px-2">to</span>
                                        <span>{{ \Carbon\Carbon::parse($range['end_date'])->format('d M Y') }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-sm py-2 px-3 bg-gray-100 rounded">
                                    No availability set by the owner
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form action="{{ route('rentals.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="bike_id" value="{{ $bike->id }}">
                        <input type="hidden" id="available_dates" value="{{ json_encode($availableDates ?? []) }}">

                        <div class="px-4 py-5 bg-white sm:p-6">
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Date Selection -->
                                <div class="col-span-1 sm:col-span-1">
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}" min="{{ date('Y-m-d') }}"
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                    @error('start_date')
                                        <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500" id="start-date-help">Please select a date from the available ranges</p>
                                </div>

                                <div class="col-span-1 sm:col-span-1">
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}" min="{{ date('Y-m-d') }}"
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                    @error('end_date')
                                        <p class="mt-1 text-red-500 text-xs">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500" id="end-date-help">End date must be within the same available range</p>
                                </div>

                                <div id="price-calculation" class="col-span-1 bg-gray-50 p-4 rounded-lg hidden">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Price Calculation</h4>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-gray-600" id="days-count">0 days</span>
                                        <span class="text-sm text-gray-600" id="daily-rate">€{{ number_format($bike->daily_rate, 2) }} per day</span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                                        <span class="text-sm font-medium text-gray-700">Total Price</span>
                                        <span class="text-sm font-semibold text-blue-600" id="total-price">€0.00</span>
                                    </div>
                                </div>

                                <div id="availability-warning" class="col-span-1 bg-yellow-50 p-4 rounded-lg border border-yellow-200 hidden">
                                    <div class="flex">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        <p class="text-sm text-yellow-700">
                                            The selected dates will be temporarily held for your booking until the owner responds to your request.
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                            <button type="submit" id="book-button" disabled class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                Book Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const priceCalculation = document.getElementById('price-calculation');
            const availabilityWarning = document.getElementById('availability-warning');
            const daysCount = document.getElementById('days-count');
            const totalPrice = document.getElementById('total-price');
            const bookButton = document.getElementById('book-button');
            const dailyRate = parseFloat("{{ $bike->daily_rate }}");
            const today = new Date().toISOString().split('T')[0];
            
            // Set minimum date for start date to today
            startDateInput.min = today;
            
            // Parse available dates from JSON
            const availableDateRanges = JSON.parse('{{ json_encode($bike->getAvailableDateRanges()) }}'.replace(/&quot;/g, '"'));
            const allAvailableDates = new Set();
            
            // Create a flat array of all available dates
            availableDateRanges.forEach(range => {
                const start = new Date(range.start_date);
                const end = new Date(range.end_date);
                
                // Add all dates in the range to the set
                for (let date = new Date(start); date <= end; date.setDate(date.getDate() + 1)) {
                    allAvailableDates.add(date.toISOString().split('T')[0]);
                }
            });
            
            function isDateInAvailableRange(dateStr) {
                // First check if date is today or in the future
                if (dateStr < today) {
                    return false;
                }
                return allAvailableDates.has(dateStr);
            }
            
            // Find range that contains a date
            function findDateRange(dateStr) {
                const checkDate = new Date(dateStr);
                
                // Don't allow past dates
                if (checkDate < new Date(today)) {
                    return null;
                }
                
                for (const range of availableDateRanges) {
                    const start = new Date(range.start_date);
                    const end = new Date(range.end_date);
                    
                    if (checkDate >= start && checkDate <= end) {
                        return { start: range.start_date, end: range.end_date };
                    }
                }
                
                return null;
            }
            
            function updateEndDateConstraints() {
                if (!startDateInput.value) {
                    endDateInput.disabled = true;
                    return;
                }
                
                const range = findDateRange(startDateInput.value);
                if (range) {
                    endDateInput.disabled = false;
                    endDateInput.min = startDateInput.value;
                    endDateInput.max = range.end;
                    
                    // Update the help text
                    document.getElementById('end-date-help').textContent = 
                        `Please select an end date (until ${new Date(range.end).toLocaleDateString()})`;
                } else {
                    endDateInput.disabled = true;
                }
            }

            function updatePriceCalculation() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (startDateInput.value && endDateInput.value && startDate <= endDate) {
                    const timeDiff = Math.abs(endDate.getTime() - startDate.getTime());
                    const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // Include both start and end day
                    const price = dailyRate * daysDiff;

                    daysCount.textContent = daysDiff + ' day' + (daysDiff !== 1 ? 's' : '');
                    totalPrice.textContent = '€' + price.toFixed(2);

                    priceCalculation.classList.remove('hidden');
                    availabilityWarning.classList.remove('hidden');
                    bookButton.disabled = false;
                } else {
                    priceCalculation.classList.add('hidden');
                    availabilityWarning.classList.add('hidden');
                    bookButton.disabled = true;
                }
            }
            
            // Update the validity of the start date
            startDateInput.addEventListener('change', function() {
                const isAvailable = isDateInAvailableRange(startDateInput.value);
                
                if (!isAvailable) {
                    startDateInput.setCustomValidity("This date is not available");
                    document.getElementById('start-date-help').textContent = "Please select an available date";
                    document.getElementById('start-date-help').classList.add('text-red-500');
                    document.getElementById('start-date-help').classList.remove('text-gray-500');
                } else {
                    startDateInput.setCustomValidity("");
                    document.getElementById('start-date-help').textContent = "Date is available";
                    document.getElementById('start-date-help').classList.add('text-green-500');
                    document.getElementById('start-date-help').classList.remove('text-red-500', 'text-gray-500');
                    
                    // Reset end date when start date changes
                    endDateInput.value = '';
                }
                
                updateEndDateConstraints();
                updatePriceCalculation();
            });

            endDateInput.addEventListener('change', function() {
                if (startDateInput.value && endDateInput.value) {
                    const startRange = findDateRange(startDateInput.value);
                    const endRange = findDateRange(endDateInput.value);
                    
                    // Verify both dates are in the same range
                    if (startRange && endRange && 
                        startRange.start === endRange.start && 
                        startRange.end === endRange.end) {
                        endDateInput.setCustomValidity("");
                        document.getElementById('end-date-help').classList.add('text-green-500');
                        document.getElementById('end-date-help').classList.remove('text-red-500', 'text-gray-500');
                    } else {
                        endDateInput.setCustomValidity("End date must be in the same availability range");
                        document.getElementById('end-date-help').textContent = "End date must be in the same availability range";
                        document.getElementById('end-date-help').classList.add('text-red-500');
                        document.getElementById('end-date-help').classList.remove('text-green-500', 'text-gray-500');
                    }
                }
                
                updatePriceCalculation();
            });

            // Initialize with end date disabled
            endDateInput.disabled = true;
            
            // Initial calculation on page load
            if (startDateInput.value && endDateInput.value) {
                updateEndDateConstraints();
                updatePriceCalculation();
            }
        });
        </script>
    @endpush
</x-app-layout>