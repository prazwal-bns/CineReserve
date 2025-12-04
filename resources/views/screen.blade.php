{{-- Screen Indicator --}}
@if(config('cine-reserve.show_screen', true))
    <div class="mb-14">
        <div class="w-full mx-auto">
            <div class="relative bg-gradient-to-b from-gray-300 to-gray-400 dark:from-gray-600 dark:to-gray-700 h-12 rounded-b-full shadow-2xl flex items-center justify-center border-t-4 border-amber-500/50">
                <span class="text-gray-800 dark:text-gray-200 font-extrabold tracking-widest text-lg opacity-80">{{ __('cine-reserve::cine-reserve.screen_label') }}</span>
            </div>
        </div>
    </div>
@endif


