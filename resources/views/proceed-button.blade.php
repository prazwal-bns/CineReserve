@if(!empty($this->selectedSeats))
    @php
        $buttonPosition = config('cine-reserve.proceed_button.position', 'right');
        $buttonColor = config('cine-reserve.proceed_button.color', 'primary');
        $justifyClass = $buttonPosition === 'left' ? 'justify-start' : 'justify-end';
        $iconPosition = config('cine-reserve.proceed_button.icon_position', 'before');
        $hasIcon = config('cine-reserve.proceed_button.has_icon', true);
        $icon = $hasIcon ? config('cine-reserve.proceed_button.icon', 'heroicon-o-arrow-right') : null;
        $outlined = config('cine-reserve.proceed_button.outlined', false);
    @endphp
    <div class="mt-6 flex {{ $justifyClass }}">
        <x-filament::button 
            wire:click="proceed"
            :icon="$icon"
            size="lg"
            color="{{ $buttonColor }}"
            iconPosition="{{ $iconPosition }}"
            :outlined="$outlined"
        >
            <span class="{{ config('cine-reserve.proceed_button.text_color', 'text-gray-900') }}">
                {{ __('cine-reserve::cine-reserve.proceed_button_label') }}
            </span>
        </x-filament::button>
    </div>
@endif
