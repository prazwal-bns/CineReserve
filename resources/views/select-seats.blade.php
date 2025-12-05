<x-filament-panels::page>
    {{-- Movie Information (conditional) --}}
    @include('cine-reserve::movie-information')

    <div class="space-y-6">
        {{-- Seat Selection --}}
        <div class="bg-white dark:bg-[#08080a] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-800">

            <h3 class="text-2xl font-bold mb-8 text-gray-900 dark:text-white text-center">{{ __('cine-reserve::cine-reserve.select_seats_title') }}</h3>

            {{-- Screen Indicator (conditional) --}}
            @include('cine-reserve::screen')

            {{-- Seat Grid --}}
            @php
            $sortedSeats = $this->seats->sortBy([
                fn($a, $b) => $a->row <=> $b->row,
                fn($a, $b) => (int) $a->number <=> (int) $b->number,
            ]);
        @endphp

        {{-- Seat Grid with Row Labels --}}
        <div class="flex-1 w-full max-w-4xl mx-auto">
            @php
            $sortedSeats = $this->seats->sortBy([
                fn($a, $b) => $a->row <=> $b->row,
                fn($a, $b) => (int) $a->number <=> (int) $b->number,
            ]);
            
            $groupedSeats = $sortedSeats->groupBy('row');
            @endphp

            <div class="space-y-4">
                @foreach ($groupedSeats as $row => $rowSeats)
                    <div class="flex items-center gap-x-6">
                        {{-- Row Label Left --}}
                        <div class="w-8 flex-shrink-0 text-center">
                            <span class="text-amber-600 dark:text-amber-400 font-extrabold text-lg tracking-wider">{{ $row }}</span>
                        </div>

                        {{-- Seats in Row --}}
                        <div class="flex gap-2 justify-between flex-1">
                            @foreach ($rowSeats as $seat)
                                @php
                                    $isSelected = in_array($seat->id, $this->selectedSeats);
                                    $isBooked = in_array($seat->id, $this->bookedSeats);
                                    
                                    // Determine seat state
                                    $state = $isBooked ? 'booked' : ($isSelected ? 'selected' : 'available');
                                    
                                    // Get color classes from component method
                                    $colors = $this->getSeatColorClasses($state);
                                @endphp
                                <button 
                                    wire:click="toggleSeat({{ $seat->id }})" 
                                    @class([
                                        'relative transition-all duration-300 transform flex-shrink-0',
                                        'hover:-translate-y-0.5 hover:shadow-xl' => !$isBooked,
                                    ])
                                    @disabled($isBooked)
                                >
                                    {{-- Seat Structure --}}
                                    <div class="relative">
                                        {{-- Backrest --}}
                                        <div 
                                            @class([
                                                'w-11 h-12 rounded-t-2xl relative overflow-hidden',
                                                $colors['backrest']['class'],
                                            ])
                                            style="{{ $colors['backrest']['style'] }}"
                                        >
                                            {{-- Leather texture highlight --}}
                                            <div class="absolute inset-0 bg-gradient-to-br from-white/20 via-transparent to-black/20"></div>
                                            
                                            {{-- Seat Number --}}
                                            <span @class([
                                                'absolute inset-0 flex items-center justify-center text-xs font-bold z-10',
                                                'text-white drop-shadow-sm' => !$isBooked,
                                                'text-gray-300' => $isBooked,
                                            ])>
                                               {{ $seat->number }}
                                            </span>

                                            {{-- Padding/cushion effect --}}
                                            <div class="absolute inset-x-2 top-2 h-3 bg-white/10 rounded-full"></div>
                                        </div>

                                        {{-- Seat Base/Cushion --}}
                                        <div 
                                            @class([
                                                'w-11 h-4 rounded-b-xl relative',
                                                $colors['base']['class'],
                                            ])
                                            style="{{ $colors['base']['style'] }}"
                                        >
                                            {{-- Cushion highlight --}}
                                            <div class="absolute inset-x-1 top-0 h-1 bg-white/20 rounded-full"></div>
                                        </div>

                                        {{-- Armrests --}}
                                        <div class="absolute top-3 left-0 right-0 flex justify-between pointer-events-none">
                                            <div @class([
                                                'w-1 h-8 rounded-full',
                                                'bg-gradient-to-b from-gray-700 to-gray-900' => !$isBooked,
                                                'bg-gradient-to-b from-gray-600 to-gray-800' => $isBooked,
                                            ])></div>
                                            <div @class([
                                                'w-1 h-8 rounded-full',
                                                'bg-gradient-to-b from-gray-700 to-gray-900' => !$isBooked,
                                                'bg-gradient-to-b from-gray-600 to-gray-800' => $isBooked,
                                            ])></div>
                                        </div>

                                        {{-- Shadow (Light Mode) --}}
                                        <div 
                                            class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-10 h-2 rounded-full blur-sm dark:hidden"
                                            style="{{ $colors['shadow']['style'] }}"
                                        ></div>
                                        {{-- Shadow (Dark Mode) --}}
                                        <div 
                                            class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-10 h-2 rounded-full blur-sm hidden dark:block"
                                            style="{{ isset($colors['shadowDark']) ? $colors['shadowDark']['style'] : $colors['shadow']['style'] }}"
                                        ></div>

                                        {{-- Selection Indicator --}}
                                        @if($isSelected)
                                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 dark:bg-green-400 rounded-full border-2 border-white dark:border-gray-900 shadow-lg z-20">
                                                <svg class="w-2.5 h-2.5 text-white absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

            {{-- Legend --}}
            <div class="mt-8 flex flex-wrap items-center gap-6 text-sm">
                @php
                    $legendColors = $this->getLegendColorClasses();
                @endphp
                <div class="flex items-center">
                    <div 
                        class="w-5 h-5 rounded-lg mr-3 {{ $legendColors['available']['border']['class'] }}"
                        style="{{ $legendColors['available']['bg']['style'] }} {{ $legendColors['available']['border']['style'] }}"
                    ></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ __('cine-reserve::cine-reserve.legend_available') }}</span>
                </div>
                <div class="flex items-center">
                    <div 
                        class="w-5 h-5 rounded-lg mr-3 shadow-sm {{ $legendColors['selected']['border']['class'] }}"
                        style="{{ $legendColors['selected']['bg']['style'] }} {{ $legendColors['selected']['border']['style'] }}"
                    ></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ __('cine-reserve::cine-reserve.legend_selected') }}</span>
                </div>
                <div class="flex items-center">
                    <div 
                        class="w-5 h-5 rounded-lg mr-3 border hidden dark:block"
                        style="{{ $legendColors['booked']['bgDark']['style'] }} {{ $legendColors['booked']['borderDark']['style'] }}"
                    ></div>
                    <div 
                        class="w-5 h-5 rounded-lg mr-3 border dark:hidden"
                        style="{{ $legendColors['booked']['bg']['style'] }} {{ $legendColors['booked']['border']['style'] }}"
                    ></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ __('cine-reserve::cine-reserve.legend_booked') }}</span>
                </div>
            </div>
        </div>

        {{-- Proceed Button --}}
        @include('cine-reserve::proceed-button')

    </div>
</x-filament-panels::page>

