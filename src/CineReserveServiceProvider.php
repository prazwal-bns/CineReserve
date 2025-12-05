<?php

namespace Przwl\CineReserve;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CineReserveServiceProvider extends PackageServiceProvider
{
    public static string $name = 'cine-reserve';
    public function configurePackage(Package $package): void
    {
        $package->name(self::$name)
            ->hasViews()
            ->hasAssets()
            ->hasTranslations()
            ->hasConfigFile('cine-reserve')
            ->hasConfigFile('cine-reserve-colors');
    }

    public function packageBooted()
    {
        parent::packageBooted();

        // Ensure config is loaded (for local development)
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cine-reserve.php',
            'cine-reserve'
        );

        FilamentAsset::register([
            Css::make('cine-reserve', __DIR__ . '/../resources/dist/css/cine-reserve.css'),
        ]);
    }
}