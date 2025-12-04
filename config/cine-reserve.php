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
    | Show Screen Indicator
    |--------------------------------------------------------------------------
    |
    | Whether to display the screen indicator above the seat grid.
    |
    */
    'show_screen' => true,

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
    | Seat Colors
    |--------------------------------------------------------------------------
    |
    | Customize the colors for different seat states using Tailwind CSS color names.
    | Available colors: emerald, amber, gray, blue, red, green, purple, etc.
    | The plugin will generate gradient classes using these base colors.
    |
    */
    'seat_colors' => [
        'available' => 'blue',  // Available seats
        'selected' => 'amber',     // Selected seats
        'booked' => 'gray',        // Booked seats
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
        'icon' => 'heroicon-o-arrow-right',
    ],
];