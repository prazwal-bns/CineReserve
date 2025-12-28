# Quick CineReserve Integration Guide

This is a sample integration guide for CineReserve. Feel free to adapt or modify these steps as you see fit for your Laravel project.

## 📦 Step 1: Install & Register

```bash
composer require przwl/cine-reserve
php artisan vendor:publish --tag=cine-reserve-config
```

Register plugin in `app/Providers/Filament/AdminPanelProvider.php`:

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

## ⚙️ Step 2: Configure Package (Optional)

Edit `config/cine-reserve.php` to configure the package:

```php
// Navigation
'register_navigation' => false,        // Show/hide navigation item

// Movie Information
'show_movie_information' => true,      // Show/hide movie information component

// Screen & Layout
'show_screen' => true,                 // Show/hide screen indicator
'select_seats_title_position' => 'left', // 'left', 'center', or 'right'

// Seat layout
'rows' => ['A', 'B', 'C', 'D', 'E'],
'seats_per_row' => 8,
'max_selection_limit' => null,         // Maximum seats per selection (null = unlimited)

// Seat colors
'seat_colors' => [
    'available' => 'green',
    'selected' => 'red',
    'booked' => 'gray',
],

// Pricing configuration
'price_per_seat' => 10.00,            // Default price per seat (all seats same price)
'show_price_per_seat' => true,         // Display price per seat in UI
'currency_symbol' => '$',              // Currency symbol ($, €, ₹, £, etc.)
```

**Note:** You can also override the `getPricePerSeat()` method in your custom `SelectSeats` class for dynamic pricing based on showtime, seat type, etc.

## 🗄️ Step 3: Database Setup

### Create Migrations

```bash
php artisan make:model Movie -m
php artisan make:model Showtime -m
php artisan make:model Booking -m
```

### Movies Migration

```php
Schema::create('movies', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('poster_url')->nullable();
    $table->text('genre')->nullable(); 
    $table->integer('duration')->nullable();
    $table->string('rating')->nullable();
    $table->timestamps();
});
```

**Note:** If you're storing multiple genres (as JSON array), use `text` type instead of `string` for the `genre` column.

### Showtimes Migration

```php
Schema::create('showtimes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('movie_id')->constrained()->onDelete('cascade');
    $table->date('date');
    $table->time('start_time');
    $table->time('end_time');
    $table->string('theater_name');
    $table->integer('total_seats')->default(40);
    $table->timestamps();
});
```

### Bookings Migration

```php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('showtime_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->json('seat_ids');
    $table->decimal('total_amount', 10, 2);
    $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
    $table->timestamps();
});
```

### Models

**Movie.php:**
```php
class Movie extends Model
{
    protected $fillable = [
        'title', 'description', 'poster_url', 
        'genre', 'duration', 'rating'
    ];
}
```

**Showtime.php:**
```php
class Showtime extends Model
{
    protected $fillable = [
        'movie_id', 'date', 'start_time', 
        'end_time', 'theater_name', 'total_seats'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
```

**Booking.php:**
```php
class Booking extends Model
{
    protected $fillable = [
        'showtime_id', 'user_id', 'seat_ids', 
        'total_amount', 'status'
    ];

    protected $casts = [
        'seat_ids' => 'array',
    ];

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

Run migrations:
```bash
php artisan migrate
```

## 📸 Step 4: Movie Information Component

### Sample Values

The movie information component automatically displays sample/demo values when no data is provided:
- **Title**: "Sample Movie Title"
- **Genre**: "Action"
- **Duration**: "120 min"
- **Rating**: "PG-13"
- **Date**: Current date (formatted)
- **Start Time**: "7:00 PM"
- **End Time**: "9:30 PM"
- **Theater**: "Theater 1"

This helps you visualize the component structure before implementing your own data. Once you set the movie properties in your `SelectSeats` class, the actual data will replace the sample values.

### Configure File Storage for Movie Posters

**Important:** Movie poster images must be stored on the `public` disk for proper display in the component.

### In Your Filament Resource Form

When creating or editing movies, configure the `FileUpload` component to use the `public` disk:

```php
use Filament\Forms\Components\FileUpload;

FileUpload::make('poster_url')
    ->image()
    ->disk('public')           // Required: Use public disk
    ->visibility('public')      // Required: Set visibility to public
    ->required(),
```

**Why is this required?** The movie information component displays images directly in the browser. Files stored on private disks cannot be accessed via direct URLs and will not display correctly. Using the `public` disk ensures that poster images are accessible and display properly.

## 🎬 Step 5: Create Custom SelectSeats Page

```bash
php artisan make:filament-page CustomSelectSeats --type=custom
```

**File: `app/Filament/Pages/CustomSelectSeats.php`**

```php
<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Showtime;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Przwl\CineReserve\Filament\Pages\SelectSeats;

class CustomSelectSeats extends SelectSeats
{
    protected static ?string $slug = 'custom-select-seats';
    
    public ?int $showtimeId = null;
    public $total = 0;

    public function mount(): void
    {
        parent::mount();
        
        $showtimeId = request()->query('showtimeId');
        
        if ($showtimeId) {
            $this->showtimeId = (int) $showtimeId;
            $this->loadShowtimeData($this->showtimeId);
        }
    }

    protected function loadShowtimeData(int $showtimeId): void
    {
        $showtime = Showtime::with('movie')->findOrFail($showtimeId);
        $movie = $showtime->movie;

        // Set movie information
        $this->movieTitle = $movie->title;
        // Generate public URL for poster (requires public disk)
        $this->moviePosterUrl = $movie->poster_url 
            ? Storage::disk('public')->url($movie->poster_url) 
            : null;
        
        // Handle genre (supports single string, array of strings, or array of enum objects)
        $this->movieGenre = $movie->genre;
        $this->movieDuration = $movie->duration ? $movie->duration . ' min' : null;
        $this->movieRating = $movie->rating;
        $this->movieDate = $showtime->date->format('F j, Y');
        $this->movieStartTime = \Carbon\Carbon::parse($showtime->start_time)->format('g:i A');
        $this->movieEndTime = \Carbon\Carbon::parse($showtime->end_time)->format('g:i A');
        $this->movieTheater = $showtime->theater_name;
        $this->moviePosterAlt = $movie->title . ' poster';

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

    public function toggleSeat($seatId): void
    {
        $seatId = (int) $seatId;

        // Prevent selecting booked seats
        if (in_array($seatId, $this->bookedSeats)) {
            Notification::make()
                ->title('Seat already booked')
                ->danger()
                ->send();
            return;
        }

        parent::toggleSeat($seatId);
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

        if (!$this->showtimeId) {
            Notification::make()
                ->title('Showtime not found')
                ->danger()
                ->send();
            return;
        }

        $this->calculateTotal();
        parent::proceed();
    }

    protected function handleBooking(array $selectedSeatDetails): void
    {
        try {
            $booking = Booking::create([
                'showtime_id' => $this->showtimeId,
                'user_id' => Auth::id(),
                'seat_ids' => $this->selectedSeats,
                'total_amount' => $this->total,
                'status' => 'pending',
            ]);

            $this->selectedSeats = [];
            $this->total = 0;

            // Reload booked seats
            $this->loadShowtimeData($this->showtimeId);

            Notification::make()
                ->title('Seats booked successfully')
                ->body("Booking ID: {$booking->id}")
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Booking failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
```

## ✅ Step 6: Test

1. Create a movie and showtime in your database
2. Access: `/admin/custom-select-seats?showtimeId=1`
3. Select seats and test booking

## 🎨 Customization

### Override Methods

- `mount()` - Load data and initialize booked seats
- `toggleSeat()` - Add validation (prevent booking already booked seats)
- `proceed()` - Add validation before booking
- `handleBooking()` - Implement booking logic (save to database, notifications)

### Pricing

The pricing feature automatically displays:
- Total price for selected seats (calculated automatically)
- Price per seat (if enabled in config)
- Selected seat labels (e.g., A1, A2, B3)

Pricing is configured in `config/cine-reserve.php`:
```php
'price_per_seat' => 10.00,        // Price per seat
'show_price_per_seat' => true,     // Show/hide price per seat in UI
'currency_symbol' => '$',          // Currency symbol
```

### Customize Views

To customize the appearance and layout of the seat selection interface, publish the views:

```bash
php artisan vendor:publish --tag=cine-reserve-views
```

This copies all view files to `resources/views/vendor/cine-reserve/` where you can modify them:

- `select-seats.blade.php` - Main seat selection page layout
- `components/movie-information.blade.php` - Movie information display component
- `pricing-display.blade.php` - Pricing information display
- `proceed-button.blade.php` - Proceed to booking button
- `screen.blade.php` - Screen indicator component

After publishing, edit these files in `resources/views/vendor/cine-reserve/` to match your design requirements. The package will automatically use your customized views instead of the default ones.

## 📝 Quick Checklist

- [ ] Install package and register plugin
- [ ] Publish config: `php artisan vendor:publish --tag=cine-reserve-config`
- [ ] Configure settings in `config/cine-reserve.php` (pricing, colors, layout, etc.)
- [ ] Create migrations and models
- [ ] Run migrations
- [ ] Configure FileUpload to use `public` disk for movie posters
- [ ] Create CustomSelectSeats page extending `SelectSeats`
- [ ] Set `$total = 0` property in your CustomSelectSeats class
- [ ] Use `Storage::disk('public')->url()` when setting `$moviePosterUrl`
- [ ] Pass genre as array if using multiple genres (component handles enum conversion)
- [ ] Register page in AdminPanelProvider
- [ ] Test seat selection, pricing display, and booking

---

**Need more details?** See the main [README.md](README.md) for advanced configuration and customization options.
