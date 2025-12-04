<?php

namespace Przwl\CineReserve\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Przwl\CineReserve\Support\SeatColorHelper;
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
     * Get seat color styles based on state
     * Delegates to SeatColorHelper for cleaner code organization
     */
    public function getSeatColorClasses(string $state): array
    {
        return SeatColorHelper::getSeatColorStyles($state);
    }

    /**
     * Get legend color styles for display
     * Delegates to SeatColorHelper for cleaner code organization
     */
    public function getLegendColorClasses(): array
    {
        return SeatColorHelper::getLegendColorStyles();
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
