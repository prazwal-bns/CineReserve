<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Show Movie Information
    |--------------------------------------------------------------------------
    |
    | Whether to display the movie information section at the top of the page.
    |
    */
    'show_movie_information' => true,

    /*
    |--------------------------------------------------------------------------
    | Movie Information Fields
    |--------------------------------------------------------------------------
    |
    | Control which fields are displayed in the movie information component.
    | Set to false to hide a field, true to show it.
    |
    */
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

    /*
    |--------------------------------------------------------------------------
    | Show Screen Indicator
    |--------------------------------------------------------------------------
    |
    | Whether to display the screen indicator above the seat grid.
    |
    */
    'show_screen' => true,

    /*
    |--------------------------------------------------------------------------
    | Select Seats Title Position
    |--------------------------------------------------------------------------
    |
    | Position of the "Select Your Seats" title.
    | Options: 'left', 'center', 'right'
    |
    */
    'select_seats_title_position' => 'left',

    /*
    |--------------------------------------------------------------------------
    | Seat Layout Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the rows and number of seats per row for the cinema.
    | Rows are specified as an array of letters/identifiers.
    | Seats per row determines how many seats are in each row.
    |
    */
    'rows' => ['A', 'B', 'C', 'D', 'E'],
    'seats_per_row' => 8,

    /*
    |--------------------------------------------------------------------------
    | Maximum Seat Selection Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of seats a user can select in a single session.
    | Set to null for unlimited selection.
    | This is a UI-level constraint. Override toggleSeat() for custom logic.
    |
    */
    'max_selection_limit' => null,

    /*
    |--------------------------------------------------------------------------
    | Seat Colors
    |--------------------------------------------------------------------------
    |
    | Customize the colors for different seat states.
    | Use color names defined in 'cine-reserve-colors.php' config file.
    | Default colors: green (available), red (selected), gray (booked)
    |
    */
    'seat_colors' => [
        'available' => 'green',
        'selected' => 'red',
        'booked' => 'gray',
    ],

    /*
    |--------------------------------------------------------------------------
    | Proceed Button Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the proceed/submit button behavior.
    | The button will emit a Livewire event 'seatSelected' with the selected seats.
    |
    */
    'proceed_button' => [
        'label' => 'Proceed to Booking',
        'has_icon' => true,
        'icon' => 'heroicon-o-arrow-right',
        'icon_position' => 'after', // 'before' or 'after'
        'color' => 'warning',  // Filament button color: primary, success, warning, danger, gray, info
        'text_color' => 'text-gray-900', // Button text color (Tailwind class, e.g., 'text-white', 'text-gray-900')
        'position' => 'right', // button position: 'left' or 'right'
        'outlined' => false, // whether to use an outlined button
    ],
];