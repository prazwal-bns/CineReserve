# CineReserve - Filament Movie Seat Selection Plugin

A seamless, user-friendly Filament plugin for adding interactive movie seat selection and booking functionality to any Laravel application.

## 🎬 Features

- **Interactive Seat Selection**: Beautiful, animated seat selection interface
- **Movie Information Display**: Showcase movie details with poster image support
- **Customizable Colors**: Choose from 6 color options (amber, gray, red, green, purple, yellow)
- **Dynamic Layout**: Configure rows and seats per row via config
- **Conditional Display**: Toggle movie information and screen indicator
- **View Publishing**: Publish views for complete customization
- **Livewire Integration**: Emits events for easy booking integration
- **Dark Mode Support**: Fully supports Filament's dark mode
- **Responsive Design**: Works on all screen sizes

## 📦 Installation

### Requirements

- PHP 8.1 or higher
- Laravel 10.0 or higher
- Filament 3.0 or higher
- Livewire 3.0 or higher

### For Local Development (Current Setup)

Since this is a local package, it's already set up via path repository in your main `composer.json`:

```json
{
    "repositories": [{
        "type": "path",
        "url": "./packages/CineReserve"
    }]
}
```

### For Production Distribution

1. Install via Composer:
```bash
composer require przwl/cine-reserve
```

2. Publish the config file (optional):
```bash
php artisan vendor:publish --tag=cine-reserve-config
```

3. Register the plugin in your Filament panel provider:

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

## ⚙️ Configuration

All configuration is done via `config/cine-reserve.php`:

### Basic Settings

```php
// Show/hide movie information section
'show_movie_information' => true,

// Show/hide screen indicator
'show_screen' => true,

// Configure seat layout
'rows' => ['A', 'B', 'C', 'D', 'E'],
'seats_per_row' => 8,
```

### Movie Information

Movie information is handled by a dedicated `MovieInformation` Blade component. Set movie data **dynamically** in your `SelectSeats` component by setting the public properties.

**Set movie data in your component:**

```php
use Przwl\CineReserve\Filament\Pages\SelectSeats;

class MySelectSeats extends SelectSeats
{
    public function mount($movieId, $showtimeId): void
    {
        parent::mount();
        
        // Load movie data from your database
        $movie = Movie::find($movieId);
        $showtime = Showtime::find($showtimeId);
        
        // Set movie information properties
        $this->movieTitle = $movie->title;
        $this->moviePosterUrl = $movie->poster_url; // or Storage::url($movie->poster_path)
        $this->movieGenre = $movie->genre;
        $this->movieDuration = $movie->duration . ' min';
        $this->movieRating = $movie->rating;
        $this->movieDate = $showtime->date->format('F j, Y');
        $this->movieTime = $showtime->time->format('g:i A');
        $this->movieTheater = $showtime->theater->name;
        $this->moviePosterAlt = $movie->title . ' poster';
        
        // Load booked seats for this showtime
        $this->bookedSeats = Booking::where('showtime_id', $showtimeId)
            ->pluck('seat_id')
            ->toArray();
    }
}
```

**Available movie properties:**
- `$moviePosterUrl` - URL or path to movie poster image
- `$movieTitle` - Movie title
- `$movieGenre` - Movie genre(s)
- `$movieDuration` - Movie duration
- `$movieRating` - Movie rating
- `$movieDate` - Show date
- `$movieTime` - Show time
- `$movieTheater` - Theater name
- `$moviePosterAlt` - Alt text for poster (default: 'Movie poster')

**Note**: 
- The `MovieInformation` component automatically handles display logic and only shows if `show_movie_information` is enabled in config and at least one property is set.
- If `moviePosterUrl` is `null` or empty, a placeholder image will be displayed.
- The poster supports both local paths (e.g., `/storage/posters/movie.jpg`) and external URLs.

### Color Customization

```php
'seat_colors' => [
    'available' => 'green',   // Available seats color
    'selected' => 'amber',     // Selected seats color
    'booked' => 'gray',       // Booked seats color
],
```

**Available Colors**: `amber`, `gray`, `red`, `green`, `purple`, `yellow`

### Proceed Button

```php
'proceed_button' => [
    'label' => 'Proceed to Booking',
    'icon' => 'heroicon-o-arrow-right',
    'position' => 'right', // 'left' or 'right' - button alignment
    'color' => 'primary',  // Filament button color: primary, success, warning, danger, gray, info
],
```

**Available Colors**: `primary`, `success`, `warning`, `danger`, `gray`, `info`

## 🎨 Customization

### Changing Seat Colors

Edit `config/cine-reserve.php`:

```php
'seat_colors' => [
    'available' => 'purple',  // Change to any of the 6 available colors
    'selected' => 'yellow',
    'booked' => 'red',
],
```

After changing, clear config cache:
```bash
php artisan config:clear
```

### Customizing Layout

Change the number of rows and seats:

```php
'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G'],  // Add more rows
'seats_per_row' => 10,  // Change seats per row
```

### Overriding Views

Publish and customize views:

```bash
php artisan vendor:publish --tag=cine-reserve-views
```

This will copy all views to `resources/views/vendor/cine-reserve/`. You can then customize:

- `select-seats.blade.php` - Main seat selection page
- `components/movie-information.blade.php` - Movie information component view
- `screen.blade.php` - Screen indicator
- `proceed-button.blade.php` - Proceed button component

**Example**: To customize the movie information component, publish views and edit `resources/views/vendor/cine-reserve/components/movie-information.blade.php`.

### Overriding Translations

Publish translations:

```bash
php artisan vendor:publish --tag=cine-reserve-translations
```

Edit `lang/vendor/cine-reserve/en/cine-reserve.php`

## 🔌 Usage

### Listening to Seat Selection Events

The plugin emits a Livewire event when users click "Proceed to Booking". Listen to it in your components:

```php
use Livewire\Attributes\On;

#[On('seatSelected')]
public function handleSeatSelection($data)
{
    $selectedSeats = $data['selectedSeats'];     // Array of seat IDs
    $seatDetails = $data['seatDetails'];         // Full seat information
    $count = $data['count'];                     // Number of selected seats
    
    // Your booking logic here
    // Example: Save to database, redirect to payment, etc.
}
```

### Extending SelectSeats

The `SelectSeats` class is designed to be simple and easily overridable. Extend it to add your own validation, business logic, and data loading:

```php
use Przwl\CineReserve\Filament\Pages\SelectSeats;
use Filament\Notifications\Notification;

class MySelectSeats extends SelectSeats
{
    public function mount($movieId, $showtimeId): void
    {
        parent::mount();
        
        // Load movie and showtime data
        $movie = Movie::find($movieId);
        $showtime = Showtime::find($showtimeId);
        
        // Set movie information
        $this->movieTitle = $movie->title;
        $this->moviePosterUrl = Storage::url($movie->poster_path);
        $this->movieGenre = $movie->genre;
        $this->movieDuration = $movie->duration . ' min';
        $this->movieRating = $movie->rating;
        $this->movieDate = $showtime->date->format('F j, Y');
        $this->movieTime = $showtime->time->format('g:i A');
        $this->movieTheater = $showtime->theater->name;
        
        // Load booked seats for this showtime
        $this->bookedSeats = Booking::where('showtime_id', $showtimeId)
            ->pluck('seat_id')
            ->toArray();
    }
}
```

### Adding Validation

Override `toggleSeat()` to add your own validation:

```php
use Filament\Notifications\Notification;

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
    
    // Add your own validation logic here
    // Example: Check max selection limit, validate seat exists, etc.
    
    // Call parent to handle toggle
    parent::toggleSeat($seatId);
}
```

### Customizing Total Calculation

Override the `calculateTotal()` method:

```php
public function calculateTotal(): void
{
    $pricePerSeat = 10.00;
    $this->total = count($this->selectedSeats) * $pricePerSeat;
}
```

### Customizing Proceed Logic

Override `proceed()` to add validation and custom behavior:

```php
public function proceed(): void
{
    // Add your validation
    if (empty($this->selectedSeats)) {
        Notification::make()
            ->title('Please select at least one seat')
            ->warning()
            ->send();
        return;
    }
    
    // Call parent to emit event, or implement your own logic
    parent::proceed();
    
    // Or redirect, save to database, etc.
    // return redirect()->route('booking.checkout');
}
```

## 📁 Package Structure

```
packages/CineReserve/
├── config/
│   ├── cine-reserve.php          # Main configuration
│   └── cine-reserve-colors.php   # Color values (internal)
├── resources/
│   ├── lang/
│   │   └── en/
│   │       └── cine-reserve.php  # Translations
│   ├── views/
│   │   ├── select-seats.blade.php      # Main seat selection view
│   │   ├── components/
│   │   │   └── movie-information.blade.php # Movie info component view
│   │   ├── screen.blade.php            # Screen indicator component
│   │   └── proceed-button.blade.php    # Proceed button component
│   ├── css/
│   │   └── cine-reserve.css      # Custom CSS (Tailwind)
│   └── dist/
│       └── css/
│           └── cine-reserve.css  # Compiled CSS
├── src/
│   ├── CineReserveServiceProvider.php  # Service provider
│   ├── Filament/
│   │   ├── CineReserve.php       # Filament plugin class
│   │   └── Pages/
│   │       └── SelectSeats.php    # Main page class
│   └── Support/
│       └── SeatColorHelper.php    # Color helper class
├── composer.json
└── README.md
```

## 🎯 Key Components

### SelectSeats Page
- Main Filament page for seat selection
- Handles seat toggling, selection state, and booking logic
- Emits `seatSelected` event on proceed

### SeatColorHelper
- Manages all color-related logic
- Self-contained color palette
- Generates inline styles to avoid Tailwind purge issues

### Views & Components
- **select-seats.blade.php**: Main seat selection interface
- **components/movie-information.blade.php**: Movie information Blade component (handles display logic)
- **screen.blade.php**: Screen indicator (optional)
- **proceed-button.blade.php**: Proceed/Submit button component

### Components
- **MovieInformation**: Blade component class (`Przwl\CineReserve\View\Components\MovieInformation`) that handles movie information display. Accepts movie data as parameters and manages display logic internally.

## 🚀 Events

### seatSelected
Emitted when user clicks "Proceed to Booking" button.

**Event Data:**
```php
[
    'selectedSeats' => [1, 2, 3],  // Array of seat IDs
    'seatDetails' => [              // Full seat information
        ['id' => 1, 'row' => 'A', 'number' => '1', 'label' => 'A1'],
        // ...
    ],
    'count' => 3                    // Number of selected seats
]
```

### seat-selection-empty
Emitted when user tries to proceed without selecting any seats (shows notification).

## 🔧 Customization

The `SelectSeats` class is intentionally simple. Override methods to add your own logic:

- `mount()` - Load data, set movie info, initialize booked seats
- `toggleSeat()` - Add validation, selection limits, business rules
- `proceed()` - Add validation, redirect, save to database
- `calculateTotal()` - Implement pricing logic

See examples above in the "Extending SelectSeats" section.

## 🎨 Color System

The plugin uses inline styles with RGB values to avoid Tailwind CSS purge issues. Colors are defined in `SeatColorHelper` class and can be customized via config.

**Supported Colors:**
- `amber` - Warm yellow/orange
- `gray` - Neutral gray
- `red` - Red
- `green` - Green
- `purple` - Purple
- `yellow` - Yellow

## 📝 Translation Keys

All text is translatable. Available keys:

- `select_seats_title` - "Select Your Seats"
- `screen_label` - "SCREEN"
- `legend_available` - "Available"
- `legend_selected` - "Selected"
- `legend_booked` - "Booked"
- `proceed_button` - "Proceed to Booking"

## 🔧 Development

### Local Development Setup

1. Package is located at `packages/CineReserve/`
2. Linked via path repository in main `composer.json`
3. Config is auto-loaded via `mergeConfigFrom()` in service provider
4. Changes reflect immediately (clear cache if needed)

### Making Changes

1. Edit files in `packages/CineReserve/`
2. Clear caches: `php artisan optimize:clear`
3. Refresh browser

## 📄 License

MIT License - see LICENSE file for details

## 👤 Author

**prazwal-bns**
- Email: prajwalbns15@gmail.com

## 🤝 Contributing

This is currently a private package. For contributions, please contact the author.

---

**Built with ❤️ for Filament v4**

