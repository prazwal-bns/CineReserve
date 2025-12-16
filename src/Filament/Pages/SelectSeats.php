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

    // should show in navigation bar
    protected static bool $shouldRegisterNavigation = false;
    
    // Movie information properties
    public $moviePosterUrl = null;
    public $movieTitle = null;
    public $movieGenre = null;
    public $movieDuration = null;
    public $movieRating = null;
    public $movieDate = null;
    public $movieStartTime = null;
    public $movieEndTime = null;
    public $movieTheater = null;
    public $moviePosterAlt = 'Movie poster';
    
    protected string $view = 'cine-reserve::select-seats';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-inbox-stack';

    protected static UnitEnum|string|null $navigationGroup = 'Seat Management';

    protected static ?string $title = 'Select Seats';

    /**
     * Initialize component. Override to load your own data.
     */
    public function mount(): void
    {
        $this->bookedSeats = [15, 16, 29]; // Example: override this
    }

    /**
     * Get movie information array.
     */
    public function getMovieInformation(): array
    {
        return [
            'posterUrl' => $this->moviePosterUrl,
            'title' => $this->movieTitle,
            'genre' => $this->movieGenre,
            'duration' => $this->movieDuration,
            'rating' => $this->movieRating,
            'date' => $this->movieDate,
            'startTime' => $this->movieStartTime,
            'endTime' => $this->movieEndTime,
            'theater' => $this->movieTheater,
            'posterAlt' => $this->moviePosterAlt,
        ];
    }

    /**
     * Generate seats from config.
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
     * Toggle seat selection. Override to add your own validation.
     */
    public function toggleSeat($seatId): void
    {
        $seatId = (int) $seatId;
        
        if (in_array($seatId, $this->selectedSeats)) {
            $this->selectedSeats = array_values(array_diff($this->selectedSeats, [$seatId]));
        } else {
            $this->selectedSeats[] = $seatId;
        }
        
        $this->selectedSeats = array_values($this->selectedSeats);
        $this->calculateTotal();
    }

    /**
     * Get color RGB values for seat state.
     */
    protected function getColorValues(string $state): array
    {
        $colorName = config("cine-reserve.seat_colors.{$state}", 'gray');
        $palette = config('cine-reserve-colors', []);
        
        return $palette[$colorName] ?? $palette['gray'] ?? [
            '400' => 'rgb(156, 163, 175)',
            '500' => 'rgb(107, 114, 128)',
            '600' => 'rgb(75, 85, 99)',
            '700' => 'rgb(55, 65, 81)',
            '800' => 'rgb(31, 41, 55)',
            '900' => 'rgb(17, 24, 39)',
        ];
    }

    /**
     * Get seat color styles for state.
     */
    public function getSeatColorClasses(string $state): array
    {
        $colors = $this->getColorValues($state);
        
        return match($state) {
            'available' => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['500']}, {$colors['600']}, {$colors['700']}); border-color: {$colors['800']};",
                'base' => "background: linear-gradient(to bottom, {$colors['600']}, {$colors['800']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.4);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
            ],
            'selected' => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['400']}, {$colors['500']}, {$colors['600']}); border-color: {$colors['700']};",
                'base' => "background: linear-gradient(to bottom, {$colors['500']}, {$colors['700']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.8);",
            ],
            'booked' => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['400']}, {$colors['500']}, {$colors['600']}); border-color: {$colors['700']};",
                'base' => "background: linear-gradient(to bottom, {$colors['500']}, {$colors['700']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.4);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
            ],
            default => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['400']}, {$colors['500']}, {$colors['600']}); border-color: {$colors['700']};",
                'base' => "background: linear-gradient(to bottom, {$colors['500']}, {$colors['700']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.4);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
            ],
        };
    }

    /**
     * Get legend color styles.
     */
    public function getLegendColorClasses(): array
    {
        $availableColors = $this->getColorValues('available');
        $selectedColors = $this->getColorValues('selected');
        $bookedColors = $this->getColorValues('booked');
        
        return [
            'available' => [
                'bg' => "background-color: {$availableColors['500']};",
                'border' => "border-color: {$availableColors['600']};",
            ],
            'selected' => [
                'bg' => "background-color: {$selectedColors['500']};",
                'border' => "border-color: {$selectedColors['600']};",
            ],
            'booked' => [
                'bg' => "background-color: {$bookedColors['400']};",
                'bgDark' => "background-color: {$bookedColors['500']};",
                'border' => "border-color: {$bookedColors['500']};",
                'borderDark' => "border-color: {$bookedColors['400']};",
            ],
        ];
    }

    /**
     * Calculate total. Override to implement pricing logic.
     */
    public function calculateTotal(): void
    {
        // Override this method
    }

    /**
     * Proceed with booking. Override to add validation and custom logic.
     */
    public function proceed(): void
    {
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
        
        $this->dispatch('seatSelected', [
            'selectedSeats' => $this->selectedSeats,
            'seatDetails' => $selectedSeatDetails,
            'count' => count($this->selectedSeats),
        ]);

        // Call hook method for custom booking logic
        $this->handleBooking($selectedSeatDetails);
    }

    /**
     * Handle booking after seat selection.
     * Override this method to add your booking logic (save to database, send notifications, etc.).
     *
     * @param array $selectedSeatDetails Array of selected seat details with id, row, number, and label
     * @return void
     */
    protected function handleBooking(array $selectedSeatDetails): void
    {
        dd($selectedSeatDetails);
    }
}
