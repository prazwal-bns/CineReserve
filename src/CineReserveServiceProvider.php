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
            ->hasConfigFile();
    }

    public function packageBooted()
    {
        parent::packageBooted();

        FilamentAsset::register([
            Css::make('cine-reserve', __DIR__ . '/../resources/css/cine-reserve.css'),
        ]);
    }
}