# CineReserve Integration Guide

Quick guide to integrate CineReserve plugin into your Laravel project.

## 📦 Installation

```bash
# If using as local package (already set up)
composer require przwl/cine-reserve

# Publish config (optional)
php artisan vendor:publish --tag=cine-reserve-config
```

## 🔌 Register Plugin

In `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Przwl\CineReserve\Filament\CineReserve;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... other config
        ->plugins([
            CineReserve::make(),
        ]);
}
```

## 🗄️ Database Structure

### Models & Migrations

#### 1. Movies Table

```bash
php artisan make:model Movie -m
```

**Migration:**
```php
Schema::create('movies', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('poster_url')->nullable();
    $table->string('genre')->nullable();
    $table->integer('duration')->nullable(); // in minutes
    $table->string('rating')->nullable();
    $table->timestamps();
});
```

**Model:**
```php
class Movie extends Model
{
    protected $fillable = [
        'title', 'description', 'poster_url', 
        'genre', 'duration', 'rating'
    ];
}
```

#### 2. Showtimes Table

```bash
php artisan make:model Showtime -m
```

**Migration:**
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

**Model:**
```php
class Showtime extends Model
{
    protected $fillable = [
        'movie_id', 'date', 'start_time', 
        'end_time', 'theater_name', 'total_seats'
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

#### 3. Bookings Table

```bash
php artisan make:model Booking -m
```

**Migration:**
```php
Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('showtime_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->json('seat_ids'); // Array of seat IDs: [1, 2, 3]
    $table->decimal('total_amount', 10, 2);
    $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
    $table->timestamps();
});
```

**Model:**
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

## 🎬 Create Custom SelectSeats Page

```bash
php artisan make:filament-page CustomSelectSeats --type=custom
```

**File: `app/Filament/Pages/CustomSelectSeats.php`**

```php
<?php

namespace App\Filament\Pages;

use Przwl\CineReserve\Filament\Pages\SelectSeats;
use Filament\Notifications\Notification;
use App\Models\Showtime;
use App\Models\Booking;

class CustomSelectSeats extends SelectSeats
{
    public ?int $showtimeId = null;

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
        $this->movieStartTime = $showtime->start_time->format('g:i A');
        $this->movieEndTime = $showtime->end_time->format('g:i A');
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

    public function calculateTotal(): void
    {
        // Implement your pricing logic
        $pricePerSeat = 10.00;
        $this->total = count($this->selectedSeats) * $pricePerSeat;
    }

    public function proceed(): void
    {
        // Validation
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

        // Calculate total
        $this->calculateTotal();

        // Call parent to dispatch event and trigger handleBooking()
        parent::proceed();
    }

    protected function handleBooking(array $selectedSeatDetails): void
    {
        // Create booking
        try {
            $booking = Booking::create([
                'showtime_id' => $this->showtimeId,
                'user_id' => Auth::id(),
                'seat_ids' => $this->selectedSeats,
                'total_amount' => $this->total,
                'status' => 'pending',
            ]);

            // Clear selected seats
            $this->selectedSeats = [];
            $this->total = 0;

            // Reload booked seats
            $this->loadShowtimeData($this->showtimeId, false);

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

## 📡 Booking Options

You have two ways to handle bookings:

### Option 1: Use `handleBooking()` Hook (Recommended)

The `handleBooking()` method is automatically called after seat selection. Override it in your custom page:

```php
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

    // Your booking logic here
    // Send notifications, clear seats, redirect, etc.
}
```

**Benefits:**
- Clean separation: validation in `proceed()`, booking logic in `handleBooking()`
- Event is dispatched automatically
- Easier to maintain

### Option 2: Listen to Seat Selection Event

Alternatively, listen to the `seatSelected` event in any Livewire component:

```php
use Livewire\Attributes\On;

#[On('seatSelected')]
public function handleSeatSelection($data)
{
    $selectedSeats = $data['selectedSeats'];
    $seatDetails = $data['seatDetails'];
    $count = $data['count'];

    // Create booking
    $booking = Booking::create([
        'showtime_id' => $this->showtimeId,
        'user_id' => auth()->id(),
        'seat_ids' => $selectedSeats,
        'total_amount' => $count * 10.00, // Your pricing
        'status' => 'pending',
    ]);

    // Redirect to payment/confirmation
    return redirect()->route('booking.payment', $booking);
}
```

## 🔗 Register Custom Page

In `app/Providers/Filament/AdminPanelProvider.php`:

```php
use App\Filament\Pages\CustomSelectSeats;

public function panel(Panel $panel): Panel
{
    return $panel
        ->pages([
            Dashboard::class,
            CustomSelectSeats::class, // Add your custom page
        ]);
}
```

## 🎨 Customizing Movie Information Component

If you need to add custom fields (director, cast, synopsis, etc.) to the movie information component:

1. **Publish the views:**
```bash
php artisan vendor:publish --tag=cine-reserve-views
```

2. **Add custom properties to your `CustomSelectSeats` class:**
```php
class CustomSelectSeats extends SelectSeats
{
    public $movieDirector = null;
    public $movieSynopsis = null;
    
    protected function loadShowtimeData(int $showtimeId): void
    {
        // ... existing code ...
        
        // Add your custom properties
        $this->movieDirector = $movie->director;
        $this->movieSynopsis = $movie->synopsis;
    }
}
```

3. **Edit the published view** (`resources/views/vendor/cine-reserve/components/movie-information.blade.php`) to add your custom fields.

4. **Update the component call** in your published `select-seats.blade.php` to pass the custom props.

See the main README.md for a complete example with code snippets.

## 🎯 Quick Setup Checklist

- [ ] Run migrations
- [ ] Create models (Movie, Showtime, Booking)
- [ ] Create CustomSelectSeats page
- [ ] Register plugin in AdminPanelProvider
- [ ] Register CustomSelectSeats page
- [ ] Test seat selection
- [ ] Implement booking logic
- [ ] Add payment/confirmation flow
- [ ] (Optional) Customize movie information component with custom fields

## 💡 Tips

- Access page with: `/admin/custom-select-seats?showtimeId=1`
- Override `mount()` to accept route parameters
- Use `$this->bookedSeats` to prevent double booking
- Use `handleBooking()` hook for booking logic (recommended)
- Override `proceed()` for validation only, then call `parent::proceed()`
- Emit custom events for additional functionality

