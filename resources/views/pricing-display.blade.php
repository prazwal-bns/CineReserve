{{-- Compact Pricing Display with Overflow Handling --}}
@if(count($this->selectedSeats) > 0)
    <div>
        <div class="flex flex-col space-y-3">
            {{-- Total Price - Most prominent --}}
            <div class="flex items-baseline space-x-3">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->formatPrice($this->total) }}</span>
                <div class="flex items-center space-x-1">
                    <span class="text-sm text-gray-600 dark:text-gray-400">for</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ count($this->selectedSeats) }} seat{{ count($this->selectedSeats) === 1 ? '' : 's' }}</span>
                </div>
            </div>

            {{-- Details Row --}}
            <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
                {{-- Seat numbers - Now with responsive overflow --}}
                <div class="flex items-start space-x-2">
                    <div class="p-1 bg-blue-100 dark:bg-blue-900/40 rounded flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        {{-- Seat numbers container with truncation and show more --}}
                        <div class="flex flex-wrap items-center gap-1 text-sm">
                            @foreach($this->selectedSeats as $seatId)
                                @php
                                    $seat = $this->seats->firstWhere('id', $seatId);
                                @endphp
                                @if($seat)
                                    <span class="font-medium text-gray-900 dark:text-white px-1.5 py-0.5 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-100 dark:border-blue-800/30">
                                        {{ $seat->row }}{{ $seat->number }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Price per seat --}}
                @if(config('cine-reserve.show_price_per_seat', true))
                    <div class="flex items-center space-x-2 md:pl-4 md:border-l md:border-gray-300 md:dark:border-gray-700">
                        <div class="p-1 bg-amber-100 dark:bg-amber-900/30 rounded">
                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $this->formatPrice($this->getPricePerSeat()) }} each</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@else
    {{-- Empty state --}}
    <div class="flex items-center space-x-3">
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">No seats selected</p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Select seats above to view pricing details</p>
        </div>
    </div>
@endif