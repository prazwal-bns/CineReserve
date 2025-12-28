@if ($shouldDisplay)
    <div
        class="bg-white dark:bg-[#08080a] rounded-xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="flex flex-col md:flex-row gap-6 p-6">
            {{-- Movie Poster --}}
            @if (($fieldVisibility['poster'] ?? true))
                <div class="flex-shrink-0">
                    @if ($posterUrl)
                        <img src="{{ $posterUrl }}" alt="{{ $posterAlt }}"
                            class="w-32 h-48 md:w-40 md:h-60 object-cover rounded-lg shadow-md ring-2 ring-gray-200 dark:ring-gray-700"
                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'160\' height=\'240\'%3E%3Crect fill=\'%239ca3af\' width=\'160\' height=\'240\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%236b7280\' font-family=\'Arial\' font-size=\'14\'%3ENo Image%3C/text%3E%3C/svg%3E';">
                    @else
                        <div
                            class="w-32 h-48 md:w-40 md:h-60 bg-gradient-to-br from-gray-300 to-gray-400 dark:from-gray-600 dark:to-gray-700 rounded-lg shadow-md flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Movie Details --}}
            <div class="flex-1 space-y-4">
                {{-- Title and Meta Info --}}
                <div>
                    @if (($fieldVisibility['title'] ?? true))
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">
                            {{ $hasData() ? ($title ?? 'Movie Title') : 'Sample Movie Title' }}
                        </h2>
                    @endif
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        @if (($fieldVisibility['genre'] ?? true))
                            @foreach ($getGenres() as $genreItem)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 font-medium">
                                    {{ $genreItem }}
                                </span>
                            @endforeach
                        @endif
                        @if (($fieldVisibility['duration'] ?? true))
                            <span class="text-gray-600 dark:text-gray-400 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $hasData() ? ($duration ?? '') : '120 min' }}
                            </span>
                        @endif
                        @if (($fieldVisibility['rating'] ?? true))
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium">
                                {{ $hasData() ? ($rating ?? '') : 'PG-13' }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Showtime Details --}}
                @if (($fieldVisibility['date'] ?? true) || ($fieldVisibility['start_time'] ?? true) || ($fieldVisibility['end_time'] ?? true) || ($fieldVisibility['theater'] ?? true))
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @if (($fieldVisibility['date'] ?? true))
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                            Date</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $hasData() ? ($date ?? '') : now()->format('F j, Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if (($fieldVisibility['start_time'] ?? true))
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                            Start Time</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $hasData() ? ($startTime ?? '') : '7:00 PM' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if (($fieldVisibility['end_time'] ?? true))
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                            End Time</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $hasData() ? ($endTime ?? '') : '9:30 PM' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if (($fieldVisibility['theater'] ?? true))
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">
                                            Theater</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $hasData() ? ($theater ?? '') : 'Theater 1' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
