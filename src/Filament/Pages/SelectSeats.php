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
    
    // Movie information properties (users can set these dynamically)
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

    public function mount(): void
    {
        // Initialize booked seats (example: seats 15, 16, 29 are booked)
        // Users can override this method or set bookedSeats from their own data source
        $this->bookedSeats = [15, 16, 29];
        
        // Movie information should be set by users in their own implementation
        // Example: Load from database in mount() method
        // $movie = Movie::find($movieId);
        // $this->movieTitle = $movie->title;
        // $this->moviePosterUrl = $movie->poster_url;
        // etc...
    }

    /**
     * Get movie information as an array for the component
     * Users can override this method to customize how movie data is passed
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
     * Get color RGB values for a state
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
     * Get seat color styles based on state
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
     * Get legend color styles
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
