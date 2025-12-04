<?php

namespace Przwl\CineReserve\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use UnitEnum;

class SelectSeats extends Page
{
    public $selectedSeats = [];
    public $bookedSeats = [];
    
    protected string $view = 'cine-reserve::select-seats';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static UnitEnum|string|null $navigationGroup = 'Seat Management';

    protected static ?string $title = 'Select Seats';

    public function mount(): void
    {
        // Initialize booked seats (example: seats 15, 16, 29 are booked)
        // Users can override this method or set bookedSeats from their own data source
        $this->bookedSeats = [15, 16, 29];
    }

    /**
     * Generate seats dynamically from config
     */
    public function getSeatsProperty(): Collection
    {
        $rows = config('cine-reserve.rows', ['A', 'B', 'C', 'D', 'E']);
        $seatsPerRow = config('cine-reserve.seats_per_row', 8);
        
        $seats = [];
        $seatId = 1;
        
        foreach ($rows as $row) {
            for ($number = 1; $number <= $seatsPerRow; $number++) {
                $seats[] = [
                    'id' => $seatId,
                    'row' => $row,
                    'number' => (string) $number,
                ];
                $seatId++;
            }
        }
        
        return collect($seats)->map(function ($seat) {
            return (object) $seat;
        });
    }

    /**
     * Toggle seat selection
     */
    public function toggleSeat($seatId): void
    {
        if (in_array($seatId, $this->selectedSeats)) {
            $this->selectedSeats = array_values(array_diff($this->selectedSeats, [$seatId]));
        } else {
            $this->selectedSeats[] = $seatId;
        }
        $this->selectedSeats = array_values($this->selectedSeats); // Re-index array
        $this->calculateTotal();
    }

    /**
     * Get seat color classes based on state
     * Returns full Tailwind class strings
     * Note: For Tailwind to detect dynamic classes, users should add them to safelist
     * or use the predefined color options in config
     */
    public function getSeatColorClasses(string $state): array
    {
        $color = config("cine-reserve.seat_colors.{$state}", 'gray');
        
        if ($state === 'available') {
            return [
                'backrest' => "bg-gradient-to-br from-{$color}-500 via-{$color}-600 to-{$color}-700 border-2 border-{$color}-800",
                'base' => "bg-gradient-to-b from-{$color}-600 to-{$color}-800",
                'shadow' => "bg-{$color}-900/40 dark:bg-{$color}-900/60",
            ];
        }
        
        if ($state === 'selected') {
            return [
                'backrest' => "bg-gradient-to-br from-{$color}-400 via-{$color}-500 to-{$color}-600 border-2 border-{$color}-700",
                'base' => "bg-gradient-to-b from-{$color}-500 to-{$color}-700",
                'shadow' => "bg-{$color}-900/60 dark:bg-{$color}-900/80",
            ];
        }
        
        if ($state === 'booked') {
            return [
                'backrest' => "bg-gradient-to-br from-{$color}-400 via-{$color}-500 to-{$color}-600 border-2 border-{$color}-700",
                'base' => "bg-gradient-to-b from-{$color}-500 to-{$color}-700",
                'shadow' => "bg-{$color}-900/40 dark:bg-{$color}-900/60",
            ];
        }
        
        // Default fallback
        return [
            'backrest' => "bg-gradient-to-br from-gray-400 via-gray-500 to-gray-600 border-2 border-gray-700",
            'base' => "bg-gradient-to-b from-gray-500 to-gray-700",
            'shadow' => "bg-gray-900/40 dark:bg-gray-900/60",
        ];
    }

    /**
     * Get legend color classes for display
     * Returns full Tailwind class strings
     */
    public function getLegendColorClasses(): array
    {
        $availableColor = config('cine-reserve.seat_colors.available', 'emerald');
        $selectedColor = config('cine-reserve.seat_colors.selected', 'amber');
        $bookedColor = config('cine-reserve.seat_colors.booked', 'gray');
        
        return [
            'available' => [
                'bg' => "bg-{$availableColor}-500",
                'border' => "border-{$availableColor}-600",
            ],
            'selected' => [
                'bg' => "bg-{$selectedColor}-500",
                'border' => "border-{$selectedColor}-600",
            ],
            'booked' => [
                'bg' => "bg-{$bookedColor}-400 dark:bg-{$bookedColor}-500",
                'border' => "border-{$bookedColor}-500 dark:border-{$bookedColor}-400",
            ],
        ];
    }

    /**
     * Calculate total (placeholder for user implementation)
     */
    public function calculateTotal(): void
    {
        // Placeholder for total calculation
        // Users can override this method to implement their own pricing logic
    }

    /**
     * Proceed with selected seats
     * Emits a Livewire event that users can listen to
     */
    public function proceed(): void
    {
        if (empty($this->selectedSeats)) {
            Notification::make()
                ->title('Please select at least one seat')
                ->danger()
                ->send();
            return;
        }

        // Get selected seat details
        $selectedSeatDetails = $this->seats
            ->whereIn('id', $this->selectedSeats)
            ->map(function ($seat) {
                return [
                    'id' => $seat->id,
                    'row' => $seat->row,
                    'number' => $seat->number,
                    'label' => $seat->row . $seat->number,
                ];
            })
            ->values()
            ->toArray();

        // Emit event with selected seats data
        $this->dispatch('seatSelected', [
            'selectedSeats' => $this->selectedSeats,
            'seatDetails' => $selectedSeatDetails,
            'count' => count($this->selectedSeats),
        ]);
    }
}
