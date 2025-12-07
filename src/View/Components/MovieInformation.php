<?php

namespace Przwl\CineReserve\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class MovieInformation extends Component
{
    /**
     * Whether to display the component
     */
    public bool $shouldDisplay;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $posterUrl = null,
        public ?string $title = null,
        public ?string $genre = null,
        public ?string $duration = null,
        public ?string $rating = null,
        public ?string $date = null,
        public ?string $startTime = null,
        public ?string $endTime = null,
        public ?string $theater = null,
        public string $posterAlt = 'Movie poster',
    ) {
        $this->shouldDisplay = config('cine-reserve.show_movie_information', true);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('cine-reserve::components.movie-information');
    }
}

