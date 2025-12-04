<x-filament-panels::page>
    @include('cine-reserve::movie-information')
    <div class="space-y-6">
        {{-- Seat Selection --}}
        <div class="bg-white dark:bg-[#08080a] rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-800">

            <h3 class="text-2xl font-bold mb-8 text-gray-900 dark:text-white text-center">🍿 Select Your Seats</h3>

            {{-- Screen Indicator --}}
            <div class="mb-14">
                <div class="w-full mx-auto">
                    <div class="relative bg-gradient-to-b from-gray-300 to-gray-400 dark:from-gray-600 dark:to-gray-700 h-12 rounded-b-full shadow-2xl flex items-center justify-center border-t-4 border-amber-500/50">
                        <span class="text-gray-800 dark:text-gray-200 font-extrabold tracking-widest text-lg opacity-80">SCREEN</span>
                    </div>
                </div>
            </div>

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
                                        <div @class([
                                            'w-11 h-12 rounded-t-2xl relative overflow-hidden',
                                            'bg-gradient-to-br from-emerald-500 via-emerald-600 to-emerald-700 border-2 border-emerald-800' => !$isSelected && !$isBooked,
                                            'bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 border-2 border-amber-700' => $isSelected,
                                            'bg-gradient-to-br from-gray-400 via-gray-500 to-gray-600 border-2 border-gray-700' => $isBooked,
                                        ])>
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
                                        <div @class([
                                            'w-11 h-4 rounded-b-xl relative',
                                            'bg-gradient-to-b from-emerald-600 to-emerald-800' => !$isSelected && !$isBooked,
                                            'bg-gradient-to-b from-amber-500 to-amber-700' => $isSelected,
                                            'bg-gradient-to-b from-gray-500 to-gray-700' => $isBooked,
                                        ])>
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

                                        {{-- Shadow --}}
                                        <div @class([
                                            'absolute -bottom-1 left-1/2 -translate-x-1/2 w-10 h-2 rounded-full blur-sm',
                                            'bg-emerald-900/40 dark:bg-emerald-900/60' => !$isSelected && !$isBooked,
                                            'bg-amber-900/60 dark:bg-amber-900/80' => $isSelected,
                                            'bg-gray-900/40 dark:bg-gray-900/60' => $isBooked,
                                        ])></div>

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
                <div class="flex items-center">
                    <div class="w-5 h-5 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg mr-3"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Available</span>
                </div>
                <div class="flex items-center">
                    <div class="w-5 h-5 bg-amber-500 border border-amber-600 rounded-lg mr-3 shadow-sm"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Selected</span>
                </div>
                <div class="flex items-center">
                    <div class="w-5 h-5 bg-gray-400 dark:bg-gray-500 border border-gray-500 dark:border-gray-400 rounded-lg mr-3"></div>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">Booked</span>
                </div>
            </div>
        </div>

        </div>
</x-filament-panels::page>
