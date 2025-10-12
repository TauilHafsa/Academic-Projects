<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Availability') }}: {{ $bike->title }}
            </h2>
            <a href="{{ route('partner.bikes.show', $bike) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                {{ __('Back to Bike Details') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Instructions</h3>
                    <p class="text-gray-600 mb-4">Use the calendar below to manage when your bike is available for rent:</p>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-green-100 border border-green-400 rounded"></div>
                            <span class="text-sm">Available dates</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-red-100 border border-red-400 rounded"></div>
                            <span class="text-sm">Unavailable dates</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-blue-100 border border-blue-400 rounded"></div>
                            <span class="text-sm">Booked dates (cannot be changed)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-yellow-100 border border-yellow-400 rounded"></div>
                            <span class="text-sm">Pending requests</span>
                        </div>
                    </div>

                    <div class="mt-4 text-sm text-gray-600">
                        <ul class="list-disc list-inside">
                            <li>Click on a date to toggle its availability</li>
                            <li>Drag across multiple dates to update them all at once</li>
                            <li>Dates with confirmed rentals or pending requests cannot be marked as unavailable</li>
                            <li>Pending rental requests will be automatically resolved when you accept or reject them</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
                x-data="availabilityCalendar({{ json_encode($availabilities) }}, {{ json_encode($rentals) }})">

                <div class="p-6">
                    <!-- Month Navigation -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex space-x-1">
                            <button x-on:click="prevMonth" class="px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button x-on:click="nextMonth" class="px-3 py-1.5 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900" x-text="currentMonthName + ' ' + currentYear"></h3>

                        <div class="flex space-x-2">
                            <button x-on:click="bulkUpdate(true)" class="px-3 py-1.5 bg-green-600 border border-transparent rounded-md text-white text-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Mark All as Available
                            </button>
                            <button x-on:click="bulkUpdate(false)" class="px-3 py-1.5 bg-red-600 border border-transparent rounded-md text-white text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Mark All as Unavailable
                            </button>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead>
                                <tr>
                                    <template x-for="day in weekDays" :key="day">
                                        <th class="px-3 py-3.5 text-sm font-semibold text-gray-900 text-center" x-text="day"></th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <template x-for="(week, weekIndex) in calendarDays" :key="weekIndex">
                                    <tr class="divide-x divide-gray-200">
                                        <template x-for="(day, dayIndex) in week" :key="dayIndex">
                                            <td class="px-3 py-4 text-sm text-gray-500 relative whitespace-nowrap">
                                                <template x-if="day">
                                                    <div
                                                        :class="{
                                                            'h-16 w-16 mx-auto flex flex-col items-center justify-center cursor-pointer relative rounded-md': true,
                                                            'bg-gray-100 text-gray-400': !isCurrentMonth(day),
                                                            'bg-green-100 border border-green-400': isAvailable(day) && isCurrentMonth(day) && !isBooked(day) && !hasPendingRequest(day),
                                                            'bg-red-100 border border-red-400': !isAvailable(day) && isCurrentMonth(day) && !isBooked(day) && !hasPendingRequest(day),
                                                            'bg-blue-100 border border-blue-400 cursor-not-allowed': isBooked(day),
                                                            'bg-yellow-100 border border-yellow-400 cursor-not-allowed': hasPendingRequest(day),
                                                            'opacity-50': isPastDate(day)
                                                        }"
                                                        x-on:mousedown="startSelection(day)"
                                                        x-on:mouseup="endSelection()"
                                                        x-on:mouseenter="updateSelection(day)"
                                                    >
                                                        <span
                                                            class="text-sm font-medium"
                                                            :class="{
                                                                'text-gray-900': isCurrentMonth(day) && !isPastDate(day),
                                                                'text-gray-400': !isCurrentMonth(day) || isPastDate(day),
                                                            }"
                                                            x-text="day.getDate()"
                                                        ></span>

                                                        <template x-if="isBooked(day) && isCurrentMonth(day)">
                                                            <span class="text-xs text-blue-800 mt-1">Booked</span>
                                                        </template>

                                                        <template x-if="hasPendingRequest(day) && isCurrentMonth(day)">
                                                            <span class="text-xs text-yellow-800 mt-1">Pending</span>
                                                        </template>

                                                        <template x-if="!isBooked(day) && !hasPendingRequest(day) && isCurrentMonth(day) && !isPastDate(day)">
                                                            <span class="text-xs mt-1"
                                                                :class="{
                                                                    'text-green-800': isAvailable(day),
                                                                    'text-red-800': !isAvailable(day)
                                                                }"
                                                                x-text="isAvailable(day) ? 'Available' : 'Unavailable'"
                                                            ></span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Status Messages -->
                    <div class="mt-4">
                        <div x-show="statusMessage" class="p-3 rounded-md" :class="statusType === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'">
                            <p x-text="statusMessage"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js code for the calendar functionality -->
    <script>
        function availabilityCalendar(availabilities, rentals) {
            return {
                weekDays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                calendarDays: [],
                availabilityData: availabilities,
                rentalData: rentals,
                isSelecting: false,
                selectionStart: null,
                selectedDates: [],
                selectingAvailable: true,
                statusMessage: '',
                statusType: 'success',

                init() {
                    this.generateCalendar();
                },

                get currentMonthName() {
                    return new Date(this.currentYear, this.currentMonth, 1).toLocaleString('default', { month: 'long' });
                },

                generateCalendar() {
                    this.calendarDays = [];

                    const firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDayOfMonth = new Date(this.currentYear, this.currentMonth + 1, 0);
                    const daysInMonth = lastDayOfMonth.getDate();

                    // Fill in days from previous month
                    let firstDayOfWeek = firstDayOfMonth.getDay();
                    let prevMonthLastDay = new Date(this.currentYear, this.currentMonth, 0).getDate();

                    // Fill in days of the current month and next month
                    let dayCount = 1;
                    let nextMonthDayCount = 1;

                    for (let week = 0; week < 6; week++) {
                        const weekDays = [];

                        for (let day = 0; day < 7; day++) {
                            if (week === 0 && day < firstDayOfWeek) {
                                // Previous month day
                                const prevMonthDay = new Date(this.currentYear, this.currentMonth - 1, prevMonthLastDay - firstDayOfWeek + day + 1);
                                weekDays.push(prevMonthDay);
                            } else if (dayCount <= daysInMonth) {
                                // Current month day
                                const currentMonthDay = new Date(this.currentYear, this.currentMonth, dayCount);
                                weekDays.push(currentMonthDay);
                                dayCount++;
                            } else {
                                // Next month day
                                const nextMonthDay = new Date(this.currentYear, this.currentMonth + 1, nextMonthDayCount);
                                weekDays.push(nextMonthDay);
                                nextMonthDayCount++;
                            }
                        }

                        this.calendarDays.push(weekDays);

                        // If we've already filled all days and the next week would be entirely in the next month, break
                        if (dayCount > daysInMonth && week > 3) {
                            break;
                        }
                    }
                },

                isCurrentMonth(date) {
                    return date.getMonth() === this.currentMonth && date.getFullYear() === this.currentYear;
                },

                isAvailable(date) {
                    const formattedDate = this.formatDate(date);
                    if (this.availabilityData[formattedDate]) {
                        return this.availabilityData[formattedDate].is_available;
                    }
                    return true; // Default to available if no record exists
                },

                hasPendingRequest(date) {
                    if (!this.isCurrentMonth(date) || this.isPastDate(date)) {
                        return false;
                    }

                    const formattedDate = this.formatDate(date);
                    if (this.availabilityData[formattedDate] && this.availabilityData[formattedDate].temporary_hold_rental_id) {
                        return true;
                    }

                    // Check for pending rentals
                    return this.rentalData.some(rental => {
                        if (rental.status !== 'pending') return false;
                        
                        const startDate = new Date(rental.start_date);
                        const endDate = new Date(rental.end_date);

                        // Reset time part for comparison
                        date.setHours(0, 0, 0, 0);
                        startDate.setHours(0, 0, 0, 0);
                        endDate.setHours(0, 0, 0, 0);

                        return date >= startDate && date <= endDate;
                    });
                },

                isBooked(date) {
                    if (!this.isCurrentMonth(date) || this.isPastDate(date)) {
                        return false;
                    }

                    return this.rentalData.some(rental => {
                        if (!['confirmed', 'ongoing'].includes(rental.status)) return false;
                        
                        const startDate = new Date(rental.start_date);
                        const endDate = new Date(rental.end_date);

                        // Reset time part for comparison
                        date.setHours(0, 0, 0, 0);
                        startDate.setHours(0, 0, 0, 0);
                        endDate.setHours(0, 0, 0, 0);

                        return date >= startDate && date <= endDate;
                    });
                },

                isPastDate(date) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    date.setHours(0, 0, 0, 0);
                    return date < today;
                },

                formatDate(date) {
                    return date.toISOString().split('T')[0];
                },

                prevMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.generateCalendar();
                    this.resetSelection();
                },

                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.generateCalendar();
                    this.resetSelection();
                },

                startSelection(date) {
                    if (!this.isCurrentMonth(date) || this.isPastDate(date) || this.isBooked(date) || this.hasPendingRequest(date)) {
                        return;
                    }

                    this.isSelecting = true;
                    this.selectionStart = new Date(date);
                    this.selectedDates = [this.formatDate(new Date(date))];
                    this.selectingAvailable = !this.isAvailable(date);
                },

                updateSelection(date) {
                    if (!this.isSelecting || !this.isCurrentMonth(date) || this.isPastDate(date) || this.isBooked(date) || this.hasPendingRequest(date)) {
                        return;
                    }

                    const current = new Date(date);
                    const start = new Date(this.selectionStart);

                    this.selectedDates = [];

                    // Determine start and end dates for the range
                    let rangeStart, rangeEnd;
                    if (current < start) {
                        rangeStart = current;
                        rangeEnd = start;
                    } else {
                        rangeStart = start;
                        rangeEnd = current;
                    }

                    // Fill in all dates in the range
                    for (let d = new Date(rangeStart); d <= rangeEnd; d.setDate(d.getDate() + 1)) {
                        const dateStr = this.formatDate(new Date(d));
                        if (!this.selectedDates.includes(dateStr)) {
                            this.selectedDates.push(dateStr);
                        }
                    }
                },

                endSelection() {
                    if (!this.isSelecting) {
                        return;
                    }

                    this.isSelecting = false;

                    if (this.selectedDates.length === 0) {
                        return;
                    }

                    this.updateAvailability(this.selectedDates, this.selectingAvailable);
                },

                resetSelection() {
                    this.isSelecting = false;
                    this.selectionStart = null;
                    this.selectedDates = [];
                },

                async updateAvailability(dates, isAvailable) {
                    if (dates.length === 0) return;

                    // Check if any booked dates or dates with pending requests are in the selection
                    const hasBookedDates = dates.some(dateStr => {
                        const date = new Date(dateStr);
                        return this.isBooked(date);
                    });

                    const hasPendingDates = dates.some(dateStr => {
                        const date = new Date(dateStr);
                        return this.hasPendingRequest(date);
                    });

                    if ((hasBookedDates || hasPendingDates) && !isAvailable) {
                        this.statusMessage = 'Cannot mark booked dates or dates with pending requests as unavailable.';
                        this.statusType = 'error';
                        setTimeout(() => {
                            this.statusMessage = '';
                        }, 3000);
                        return;
                    }

                    try {
                        const response = await fetch("{{ route('partner.bikes.update-availability', $bike) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                dates: dates,
                                is_available: isAvailable
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update local data
                            dates.forEach(dateStr => {
                                if (!this.availabilityData[dateStr]) {
                                    this.availabilityData[dateStr] = { 
                                        bike_id: "{{ $bike->id }}", 
                                        date: dateStr,
                                        temporary_hold_rental_id: null 
                                    };
                                }
                                this.availabilityData[dateStr].is_available = isAvailable;
                            });

                            this.statusMessage = data.message;
                            this.statusType = 'success';
                        } else {
                            this.statusMessage = data.message || 'An error occurred while updating availability.';
                            this.statusType = 'error';
                        }
                    } catch (error) {
                        console.error('Error updating availability:', error);
                        this.statusMessage = 'An error occurred while updating availability.';
                        this.statusType = 'error';
                    }

                    setTimeout(() => {
                        this.statusMessage = '';
                    }, 3000);
                },

                bulkUpdate(isAvailable) {
                    const dates = [];

                    // Get all dates of the current month
                    const firstDayOfMonth = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDayOfMonth = new Date(this.currentYear, this.currentMonth + 1, 0);

                    for (let d = new Date(firstDayOfMonth); d <= lastDayOfMonth; d.setDate(d.getDate() + 1)) {
                        const date = new Date(d);
                        if (!this.isPastDate(date) && !this.isBooked(date) && !this.hasPendingRequest(date)) {
                            dates.push(this.formatDate(date));
                        }
                    }

                    if (dates.length > 0) {
                        this.updateAvailability(dates, isAvailable);
                    }
                }
            };
        }
    </script>
</x-app-layout>