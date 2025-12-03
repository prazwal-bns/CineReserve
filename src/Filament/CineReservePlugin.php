<?php

namespace Przwl\CineReserve\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Przwl\CineReserve\Filament\Pages\SelectSeats;

class CineReservePlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'cine-reserve';
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            SelectSeats::class
        ]);
    }

    public function boot(Panel $panel): void
    {
        // nothing to boot for now
    }
}