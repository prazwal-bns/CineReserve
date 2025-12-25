{{-- Compact Pricing Display (no background - parent container handles it) --}}
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
            <div class="flex items-center space-x-4">
                {{-- Seat numbers --}}
                <div class="flex items-center space-x-2">
                    <div class="p-1 bg-blue-100 dark:bg-blue-900/40 rounded">
                        <svg class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="flex items-center space-x-1 text-sm">
                        @foreach($this->selectedSeats as $seatId)
                            @php
                                $seat = $this->seats->firstWhere('id', $seatId);
                            @endphp
                            @if($seat)
                                <span class="font-medium text-gray-900 dark:text-white">{{ $seat->row }}{{ $seat->number }}</span>
                                @if(!$loop->last)
                                    <span class="text-gray-400">,</span>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Price per seat --}}
                @if(config('cine-reserve.show_price_per_seat', true))
                    <div class="flex items-center space-x-2">
                        <div class="w-px h-3 bg-gray-300 dark:bg-gray-700"></div>
                        <div class="p-1 bg-amber-100 dark:bg-amber-900/30 rounded">
                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <div>
        <div class="flex items-center space-x-3">
            <div class="p-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">No seats selected</p>
                <p class="text-xs text-gray-500 dark:text-gray-500">Select seats to see pricing</p>
            </div>
        </div>
    </div>
@endif