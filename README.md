# CineReserve - Filament Movie Seat Selection Plugin

A seamless, user-friendly Filament plugin for adding interactive movie seat selection and booking functionality to any Laravel application.

## 🎬 Features

- **Interactive Seat Selection**: Beautiful, animated seat selection interface
- **Movie Information Display**: Showcase movie details with poster image support
- **Customizable Colors**: Choose seat colors for booked, available and selected seats.
- **Dynamic Layout**: Configure rows and seats per row via config
- **Maximum Selection Limit**: Set limits on seat selection per session
- **Dark Mode Support**: Fully supports Filament's dark mode
- **Responsive Design**: Works on all screen sizes
- **Extensible**: Easy to extend and customize

## 📦 Installation

### Install via Composer

```bash
composer require przwl/cine-reserve
```

### Register Plugin

In `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Przwl\CineReserve\Filament\CineReserve;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            CineReserve::make(),
        ]);
}
```

### Publish Config (Optional)

```bash
php artisan vendor:publish --tag=cine-reserve-config
```

## ⚙️ Quick Configuration

Edit `config/cine-reserve.php`:

```php
// Seat layout
'rows' => ['A', 'B', 'C', 'D', 'E'],
'seats_per_row' => 8,

// Maximum seats per selection (null = unlimited)
'max_selection_limit' => null,

// Seat colors
'seat_colors' => [
    'available' => 'green',
    'selected' => 'red',
    'booked' => 'gray',
],

// Movie information fields visibility
'movie_information_fields' => [
    'poster' => true,
    'title' => true,
    'genre' => true,
    'duration' => true,
    'rating' => true,
    'date' => true,
    'start_time' => true,
    'end_time' => true,
    'theater' => true,
],
```

## 🚀 Quick Start

### 1. Create Custom SelectSeats Page

```bash
php artisan make:filament-page CustomSelectSeats --type=custom
```

### 2. Extend SelectSeats Class

```php
namespace App\Filament\Pages;

use Przwl\CineReserve\Filament\Pages\SelectSeats;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Booking;

class CustomSelectSeats extends SelectSeats
{
    public ?int $showtimeId = null;
    public $total = 0;

    public function mount(?int $showtimeId = null): void
    {
        parent::mount();
        
        if ($showtimeId) {
            $this->showtimeId = $showtimeId;
            $this->loadShowtimeData($showtimeId);
        }
    }

    protected function loadShowtimeData(int $showtimeId): void
    {
        $showtime = Showtime::with('movie')->findOrFail($showtimeId);
        $movie = $showtime->movie;

        // Set movie information
        $this->movieTitle = $movie->title;
        $this->moviePosterUrl = $movie->poster_url;
        $this->movieGenre = $movie->genre;
        $this->movieDuration = $movie->duration . ' min';
        $this->movieRating = $movie->rating;
        $this->movieDate = $showtime->date->format('F j, Y');
        $this->movieStartTime = \Carbon\Carbon::parse($showtime->start_time)->format('g:i A');
        $this->movieEndTime = \Carbon\Carbon::parse($showtime->end_time)->format('g:i A');
        $this->movieTheater = $showtime->theater_name;

        // Load booked seats
        $this->bookedSeats = Booking::where('showtime_id', $showtimeId)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->pluck('seat_ids')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }

    public function calculateTotal(): void
    {
        $pricePerSeat = 10.00;
        $this->total = count($this->selectedSeats) * $pricePerSeat;
    }

    public function proceed(): void
    {
        if (empty($this->selectedSeats)) {
            Notification::make()
                ->title('Please select at least one seat')
                ->warning()
                ->send();
            return;
        }

        $this->calculateTotal();
        parent::proceed();
    }

    protected function handleBooking(array $selectedSeatDetails): void
    {
        // Create booking
        $booking = Booking::create([
            'showtime_id' => $this->showtimeId,
            'user_id' => Auth::id(),
            'seat_ids' => $this->selectedSeats,
            'total_amount' => $this->total,
            'status' => 'pending',
        ]);

        $this->selectedSeats = [];
        $this->total = 0;

        Notification::make()
            ->title('Seats booked successfully')
            ->success()
            ->send();
    }
}
```


## 📖 Complete Integration Guide

For detailed integration instructions, database migrations, models, and advanced customization, see the [Integration Guide](INTEGRATION_GUIDE.md).

## 🎨 Customization

### Override Methods

The `SelectSeats` class is designed to be easily extensible:

- `mount()` - Load data and initialize booked seats
- `toggleSeat()` - Add validation (e.g., prevent booking already booked seats)
- `calculateTotal()` - Implement pricing logic
- `proceed()` - Add validation before booking
- `handleBooking()` - Implement booking logic (save to database, notifications, etc.)

### Publish Views

```bash
php artisan vendor:publish --tag=cine-reserve-views
```

Customize views in `resources/views/vendor/cine-reserve/`

### Publish Translations

```bash
php artisan vendor:publish --tag=cine-reserve-translations
```

## 🎯 Events

### seatSelected

Emitted when user clicks "Proceed to Booking":

```php
[
    'selectedSeats' => [1, 2, 3],  // Array of seat IDs
    'seatDetails' => [              // Full seat information
        ['id' => 1, 'row' => 'A', 'number' => '1', 'label' => 'A1'],
    ],
    'count' => 3                    // Number of selected seats
]
```

## 📝 Available Movie Properties

Set these properties in your `SelectSeats` component:

- `$moviePosterUrl` - URL or path to movie poster
- `$movieTitle` - Movie title
- `$movieGenre` - Movie genre
- `$movieDuration` - Movie duration
- `$movieRating` - Movie rating
- `$movieDate` - Show date
- `$movieStartTime` - Show start time
- `$movieEndTime` - Show end time
- `$movieTheater` - Theater name
- `$moviePosterAlt` - Alt text for poster

## 🎨 Color Options

Available seat colors: `amber`, `gray`, `red`, `green`, `purple`, `yellow`

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details

## 👤 Author

**prazwal-bns**
- Email: prajwalbns15@gmail.com
- GitHub: [@prazwal-bns](https://github.com/prazwal-bns)

---

**Built with ❤️ By Prajwal**
