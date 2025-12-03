<?php

namespace Przwl\CineReserve;

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
            ->hasConfigFile();
    }

    public function packageBooted()
    {
        parent::packageBooted();
    }
}