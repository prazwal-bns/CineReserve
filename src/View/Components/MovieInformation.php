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
     * Field visibility settings from config
     */
    public array $fieldVisibility;

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
        $this->fieldVisibility = config('cine-reserve.movie_information_fields', [
            'poster' => true,
            'title' => true,
            'genre' => true,
            'duration' => true,
            'rating' => true,
            'date' => true,
            'start_time' => true,
            'end_time' => true,
            'theater' => true,
        ]);
    }

    /**
     * Check if a field should be displayed
     */
    public function shouldShowField(string $field): bool
    {
        return $this->fieldVisibility[$field] ?? true;
    }

    /**
     * Check if there's any meaningful data to display
     */
    public function hasData(): bool
    {
        return !empty($this->title) 
            || !empty($this->posterUrl)
            || !empty($this->genre)
            || !empty($this->duration)
            || !empty($this->rating)
            || !empty($this->date)
            || !empty($this->startTime)
            || !empty($this->endTime)
            || !empty($this->theater);
    }

    /**
     * Get display value with fallback to sample data
     */
    public function getDisplayValue(string $property, string $sampleValue): string
    {
        return !empty($this->$property) ? $this->$property : $sampleValue;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('cine-reserve::components.movie-information');
    }
}

