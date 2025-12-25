<?php

namespace Przwl\CineReserve\Filament\Pages\Concerns;

use Illuminate\Support\Collection;

trait HasSeatSelection
{
    /**
     * Generate seats from config.
     */
    public function getSeatsProperty(): Collection
    {
        $rows = config('cine-reserve.rows', ['A', 'B', 'C', 'D', 'E']);
        $seatsPerRow = config('cine-reserve.seats_per_row', 8);
        
        $seats = [];
        $seatId = 1;
        
        foreach ($rows as $row) {
            for ($number = 1; $number <= $seatsPerRow; $number++) {
                $seats[] = [
                    'id' => $seatId,
                    'row' => $row,
                    'number' => (string) $number,
                ];
                $seatId++;
            }
        }
        
        return collect($seats)->map(function ($seat) {
            return (object) $seat;
        });
    }

    /**
     * Get color RGB values for seat state.
     */
    protected function getColorValues(string $state): array
    {
        $colorName = config("cine-reserve.seat_colors.{$state}", 'gray');
        $palette = config('cine-reserve-colors', []);
        
        return $palette[$colorName] ?? $palette['gray'] ?? [
            '400' => 'rgb(156, 163, 175)',
            '500' => 'rgb(107, 114, 128)',
            '600' => 'rgb(75, 85, 99)',
            '700' => 'rgb(55, 65, 81)',
            '800' => 'rgb(31, 41, 55)',
            '900' => 'rgb(17, 24, 39)',
        ];
    }

    /**
     * Get seat color styles for state.
     */
    public function getSeatColorClasses(string $state): array
    {
        $colors = $this->getColorValues($state);
        
        return match($state) {
            'available' => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['500']}, {$colors['600']}, {$colors['700']}); border-color: {$colors['800']};",
                'base' => "background: linear-gradient(to bottom, {$colors['600']}, {$colors['800']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.4);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
            ],
            'selected' => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['400']}, {$colors['500']}, {$colors['600']}); border-color: {$colors['700']};",
                'base' => "background: linear-gradient(to bottom, {$colors['500']}, {$colors['700']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.8);",
            ],
            'booked' => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['400']}, {$colors['500']}, {$colors['600']}); border-color: {$colors['700']};",
                'base' => "background: linear-gradient(to bottom, {$colors['500']}, {$colors['700']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.4);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
            ],
            default => [
                'backrest' => "background: linear-gradient(to bottom right, {$colors['400']}, {$colors['500']}, {$colors['600']}); border-color: {$colors['700']};",
                'base' => "background: linear-gradient(to bottom, {$colors['500']}, {$colors['700']});",
                'shadow' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.4);",
                'shadowDark' => "background-color: rgba(" . str_replace(['rgb(', ')'], '', $colors['900']) . ", 0.6);",
            ],
        };
    }

    /**
     * Get legend color styles.
     */
    public function getLegendColorClasses(): array
    {
        $availableColors = $this->getColorValues('available');
        $selectedColors = $this->getColorValues('selected');
        $bookedColors = $this->getColorValues('booked');
        
        return [
            'available' => [
                'bg' => "background-color: {$availableColors['500']};",
                'border' => "border-color: {$availableColors['600']};",
            ],
            'selected' => [
                'bg' => "background-color: {$selectedColors['500']};",
                'border' => "border-color: {$selectedColors['600']};",
            ],
            'booked' => [
                'bg' => "background-color: {$bookedColors['400']};",
                'bgDark' => "background-color: {$bookedColors['500']};",
                'border' => "border-color: {$bookedColors['500']};",
                'borderDark' => "border-color: {$bookedColors['400']};",
            ],
        ];
    }
}

