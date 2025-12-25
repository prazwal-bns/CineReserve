<?php

namespace Przwl\CineReserve;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Przwl\CineReserve\View\Components\MovieInformation;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CineReserveServiceProvider extends PackageServiceProvider
{
    public static string $name = 'cine-reserve';
    public function configurePackage(Package $package): void
    {
        $package->name(self::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasConfigFile('cine-reserve');
    }

    public function packageRegistered(): void
    {
        // This ensures both config files can be published with the same tag
        $this->publishes([
            __DIR__ . '/../config/cine-reserve-colors.php' => config_path('cine-reserve-colors.php'),
        ], 'cine-reserve-config');
    }

    public function packageBooted()
    {
        parent::packageBooted();
        
        // cine-reserve.php is already merged by hasConfigFile() above
        // Only need to merge the second config file manually
        $this->mergeConfigFrom(
            __DIR__ . '/../config/cine-reserve-colors.php',
            'cine-reserve-colors'
        );

        // Register Blade component
        Blade::component('cine-reserve::movie-information', MovieInformation::class);

        // Register CSS asset (only if file exists)
        $cssPath = __DIR__ . '/../resources/dist/css/cine-reserve.css';
        if (file_exists($cssPath)) {
            FilamentAsset::register([
                Css::make('cine-reserve', $cssPath),
            ]);
        }
    }
}