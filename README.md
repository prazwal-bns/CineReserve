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

    // Maximum seats that can be selected in one session
    // Set to null for unlimited selection
    'max_selection_limit' => null,
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
        $this->movieStartTime = \Carbon\Carbon::parse($showtime->start_time)->format('g:i A');
        $this->movieEndTime = \Carbon\Carbon::parse($showtime->end_time)->format('g:i A');
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
- `$movieStartTime` - Show start time
- `$movieEndTime` - Show end time
- `$movieTheater` - Theater name
- `$moviePosterAlt` - Alt text for poster (default: 'Movie poster')

**Note**: 
- The `MovieInformation` component automatically handles display logic and only shows if `show_movie_information` is enabled in config and at least one property is set.
- If `moviePosterUrl` is `null` or empty, a placeholder image will be displayed.
- The poster supports both local paths (e.g., `/storage/posters/movie.jpg`) and external URLs.

### Customizing Movie Information Fields

Control which fields are displayed in the movie information component:

```php
'movie_information_fields' => [
    'poster' => true,        // Show/hide movie poster
    'title' => true,         // Show/hide movie title
    'genre' => true,         // Show/hide genre badge
    'duration' => true,      // Show/hide duration
    'rating' => true,        // Show/hide rating
    'date' => true,          // Show/hide show date
    'start_time' => true,    // Show/hide start time
    'end_time' => true,      // Show/hide end time
    'theater' => true,       // Show/hide theater name
],
```

**Example**: Hide rating and end time:

```php
'movie_information_fields' => [
    'poster' => true,
    'title' => true,
    'genre' => true,
    'duration' => true,
    'rating' => false,       // Hidden
    'date' => true,
    'start_time' => true,
    'end_time' => false,    // Hidden
    'theater' => true,
],
```

Fields will only display if:
1. The field is enabled in config (`true`)
2. The corresponding property has a value (not `null` or empty)

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

#### Customizing Movie Information Component

The movie information component can be fully customized by publishing its view. This allows you to:
- Add custom fields (director, cast, synopsis, language, etc.)
- Change the layout and styling
- Reorder or remove existing fields
- Add custom HTML/JavaScript

**Complete Example**: Adding custom "Director" and "Synopsis" fields:

1. **Publish the views:**
```bash
php artisan vendor:publish --tag=cine-reserve-views
```

2. **Add custom properties to your `SelectSeats` component:**
```php
class MySelectSeats extends SelectSeats
{
    public $movieDirector = null;
    public $movieSynopsis = null;
    
    public function mount($movieId, $showtimeId): void
    {
        parent::mount();
        $movie = Movie::find($movieId);
        
        // Set standard properties
        $this->movieTitle = $movie->title;
        $this->moviePosterUrl = $movie->poster_url;
        // ... other standard properties
        
        // Set your custom properties
        $this->movieDirector = $movie->director;
        $this->movieSynopsis = $movie->synopsis;
    }
}
```

3. **Edit the published view** (`resources/views/vendor/cine-reserve/components/movie-information.blade.php`):
```blade
{{-- Add your custom fields anywhere in the component --}}
@if ($movieDirector ?? null)
    <div class="flex items-start gap-3 mt-4">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">Director</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $movieDirector }}</p>
        </div>
    </div>
@endif

@if ($movieSynopsis ?? null)
    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-2">Synopsis</p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $movieSynopsis }}</p>
    </div>
@endif
```

4. **Update the component call** in your published `select-seats.blade.php` (if you also published that view):
```blade
<x-cine-reserve::movie-information
    :poster-url="$this->moviePosterUrl"
    :title="$this->movieTitle"
    :genre="$this->movieGenre"
    :duration="$this->movieDuration"
    :rating="$this->movieRating"
    :date="$this->movieDate"
    :start-time="$this->movieStartTime"
    :end-time="$this->movieEndTime"
    :theater="$this->movieTheater"
    :poster-alt="$this->moviePosterAlt"
    :director="$this->movieDirector"      {{-- Add custom prop --}}
    :synopsis="$this->movieSynopsis"       {{-- Add custom prop --}}
/>
```

**Important Notes**: 
- When you publish views, Laravel will automatically use your published version instead of the package's default view.
- The component accepts any additional properties you pass to it - you're not limited to the predefined ones.
- You can completely rewrite the component view to match your design requirements.
- Use the null coalescing operator (`??`) in Blade to safely check for custom properties that may not always be set.

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
    
    // Check max selection limit (if using config)
    $maxLimit = config('cine-reserve.max_selection_limit');
    if ($maxLimit !== null && count($this->selectedSeats) >= $maxLimit && !in_array($seatId, $this->selectedSeats)) {
        Notification::make()
            ->title('Maximum seats reached')
            ->body("You can only select up to {$maxLimit} seat(s).")
            ->warning()
            ->send();
        return;
    }
    
    // Call parent to handle toggle
    parent::toggleSeat($seatId);
}
```

**Note**: The base `toggleSeat()` method silently enforces `max_selection_limit` if set in config. Override it to add custom notifications or validation logic.

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

You have two options for handling the booking process:

#### Option 1: Override `proceed()` (Full Control)

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

#### Option 2: Use `handleBooking()` Hook (Recommended)

Keep validation in `proceed()` and place booking logic to `handleBooking()`:

```php
public function proceed(): void
{
    // Validation only
    if (empty($this->selectedSeats)) {
        Notification::make()
            ->title('Please select at least one seat')
            ->warning()
            ->send();
        return;
    }
    
    // Validate showtime exists
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
    // Create booking in database
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
    
    // Show success notification
    Notification::make()
        ->title('Seats booked successfully')
        ->body("Booking ID: {$booking->id}")
        ->success()
        ->send();
}
```

**Benefits of using `handleBooking()`:**
- Clean separation: validation in `proceed()`, business logic in `handleBooking()`
- Event is dispatched automatically before booking logic runs
- Easier to maintain and test
- Follows the plugin's intended pattern

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
│   └── View/
│       └── Components/
│           └── MovieInformation.php    # Movie information Blade component
├── composer.json
└── README.md
```

## 🎯 Key Components

### SelectSeats Page
- Main Filament page for seat selection
- Handles seat toggling, selection state, and booking logic
- Emits `seatSelected` event on proceed

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
- `proceed()` - Add validation before booking (recommended: call `parent::proceed()` to trigger `handleBooking()`)
- `handleBooking()` - Implement booking logic (save to database, send notifications, etc.)
- `calculateTotal()` - Implement pricing logic

See examples above in the "Extending SelectSeats" section.

## 🎨 Color System

The plugin uses inline styles with RGB values to avoid Tailwind CSS purge issues. Colors are defined in `cine-reserve-colors.php` config file and can be customized.

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

**Built with ❤️ By Prajwal**

