@if(!empty($this->selectedSeats))
    @php
        $buttonPosition = config('cine-reserve.proceed_button.position', 'right');
        $buttonColor = config('cine-reserve.proceed_button.color', 'primary');
        $justifyClass = $buttonPosition === 'left' ? 'justify-start' : 'justify-end';
        $iconPosition = config('cine-reserve.proceed_button.icon_position', 'before');
    @endphp
    <div class="mt-6 flex {{ $justifyClass }}">
        <x-filament::button 
            wire:click="proceed"
            icon="{{ config('cine-reserve.proceed_button.icon', 'heroicon-o-arrow-right') }}"
            size="lg"
            color="{{ $buttonColor }}"
            iconPosition="{{ $iconPosition }}"
        >
            <span class="{{ config('cine-reserve.proceed_button.text_color', 'text-white') }} font-semibold">
                {{ config('cine-reserve.proceed_button.label', __('cine-reserve::cine-reserve.proceed_button')) }}
            </span>
        </x-filament::button>
    </div>
@endif