<?php

namespace Przwl\CineReserve\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Przwl\CineReserve\Filament\Pages\Concerns\HasSeatSelection;
use UnitEnum;

class SelectSeats extends Page
{
    use HasSeatSelection;
    public $selectedSeats = [];
    public $bookedSeats = [];
    public $total = 0;
    
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
     * Determine if the page should be registered in navigation.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return config('cine-reserve.register_navigation', false);
    }

    /**
     * Initialize component. Override to load your own data.
     */
    public function mount(): void
    {
        $this->bookedSeats = [15, 16, 29]; // Example: override this
    }

    /**
     * Get movie information array.
     * Override this method to customize the movie information structure.
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
     * Toggle seat selection. Override to add your own validation.
     */
    public function toggleSeat($seatId): void
    {
        $seatId = (int) $seatId;
        
        if (in_array($seatId, $this->selectedSeats)) {
            // Deselect seat
            $this->selectedSeats = array_values(array_diff($this->selectedSeats, [$seatId]));
        } else {
            // Check max selection limit
            $maxLimit = config('cine-reserve.max_selection_limit');
            if ($maxLimit !== null && count($this->selectedSeats) >= $maxLimit) {
                Notification::make()
                    ->title('Maximum selection limit reached')
                    ->body('You can only select up to ' . $maxLimit . ' seats at a time.')
                    ->warning()
                    ->send();
                return;
            }
            
            $this->selectedSeats[] = $seatId;
        }
        
        $this->selectedSeats = array_values($this->selectedSeats);
        $this->calculateTotal();
    }


    /**
     * Get price per seat.
     * Override this method for custom pricing logic.
     *
     * @return float
     */
    public function getPricePerSeat(): float
    {
        return (float) config('cine-reserve.price_per_seat', 10.00);
    }

    /**
     * Calculate total. Override to implement custom pricing logic.
     */
    public function calculateTotal(): void
    {
        $pricePerSeat = $this->getPricePerSeat();
        $this->total = count($this->selectedSeats) * $pricePerSeat;
    }

    /**
     * Format price for display.
     *
     * @param float $price
     * @return string
     */
    public function formatPrice(float $price): string
    {
        $symbol = config('cine-reserve.currency_symbol', '$');
        return $symbol . number_format($price, 2);
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
        
        // Calculate total before dispatching
        $this->calculateTotal();
        
        $this->dispatch('seatSelected', [
            'selectedSeats' => $this->selectedSeats,
            'seatDetails' => $selectedSeatDetails,
            'count' => count($this->selectedSeats),
            'total' => $this->total,
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
        if (empty($this->selectedSeats)) {
            return;
        }

        // Mark selected seats as booked
        $this->bookedSeats = array_unique(array_merge($this->bookedSeats, $this->selectedSeats));
        $this->bookedSeats = array_values($this->bookedSeats);

        // Format seat labels for notification
        $seatLabels = collect($selectedSeatDetails)->pluck('label')->join(', ');
        $seatCount = count($this->selectedSeats);

        // Clear selected seats
        $this->selectedSeats = [];
        $this->calculateTotal();

        // Send success notification
        Notification::make()
            ->title('Seats booked successfully')
            ->body($seatCount === 1 
                ? "Seat {$seatLabels} has been booked."
                : "Seats {$seatLabels} have been booked.")
            ->success()
            ->send();
    }
}
