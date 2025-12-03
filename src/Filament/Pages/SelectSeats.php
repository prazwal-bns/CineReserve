<?php

namespace Przwl\CineReserve\Filament\Pages;

use BackedEnum;
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

    // Static array of seats
    protected static array $seatsData = [
        ['id' => 1, 'row' => 'A', 'number' => '1'],
        ['id' => 2, 'row' => 'A', 'number' => '2'],
        ['id' => 3, 'row' => 'A', 'number' => '3'],
        ['id' => 4, 'row' => 'A', 'number' => '4'],
        ['id' => 5, 'row' => 'A', 'number' => '5'],
        ['id' => 6, 'row' => 'A', 'number' => '6'],
        ['id' => 7, 'row' => 'A', 'number' => '7'],
        ['id' => 8, 'row' => 'A', 'number' => '8'],
        ['id' => 9, 'row' => 'B', 'number' => '1'],
        ['id' => 10, 'row' => 'B', 'number' => '2'],
        ['id' => 11, 'row' => 'B', 'number' => '3'],
        ['id' => 12, 'row' => 'B', 'number' => '4'],
        ['id' => 13, 'row' => 'B', 'number' => '5'],
        ['id' => 14, 'row' => 'B', 'number' => '6'],
        ['id' => 15, 'row' => 'B', 'number' => '7'],
        ['id' => 16, 'row' => 'B', 'number' => '8'],
        ['id' => 17, 'row' => 'C', 'number' => '1'],
        ['id' => 18, 'row' => 'C', 'number' => '2'],
        ['id' => 19, 'row' => 'C', 'number' => '3'],
        ['id' => 20, 'row' => 'C', 'number' => '4'],
        ['id' => 21, 'row' => 'C', 'number' => '5'],
        ['id' => 22, 'row' => 'C', 'number' => '6'],
        ['id' => 23, 'row' => 'C', 'number' => '7'],
        ['id' => 24, 'row' => 'C', 'number' => '8'],
        ['id' => 25, 'row' => 'D', 'number' => '1'],
        ['id' => 26, 'row' => 'D', 'number' => '2'],
        ['id' => 27, 'row' => 'D', 'number' => '3'],
        ['id' => 28, 'row' => 'D', 'number' => '4'],
        ['id' => 29, 'row' => 'D', 'number' => '5'],
        ['id' => 30, 'row' => 'D', 'number' => '6'],
        ['id' => 31, 'row' => 'D', 'number' => '7'],
        ['id' => 32, 'row' => 'D', 'number' => '8'],
        ['id' => 33, 'row' => 'E', 'number' => '1'],
    ];

    public function mount(): void
    {
        // Initialize booked seats (example: seats 15, 16, 29 are booked)
        $this->bookedSeats = [15, 16, 29];
    }

    public function getSeatsProperty(): Collection
    {
        return collect(static::$seatsData)->map(function ($seat) {
            return (object) $seat;
        });
    }

    // toggle seat selection
    public function toggleSeat($seatId): void
    {
        if (in_array($seatId, $this->selectedSeats)) {
            $this->selectedSeats = array_diff($this->selectedSeats, [$seatId]);
        } else {
            $this->selectedSeats[] = $seatId;
        }
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        // Placeholder for total calculation
        // You can implement this later
    }
}
