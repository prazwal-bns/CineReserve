<?php

namespace Przwl\CineReserve;

use Illuminate\Support\ServiceProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CineReserveServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        throw new \Exception('Not implemented');
    }
}