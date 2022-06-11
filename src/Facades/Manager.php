<?php

namespace AppManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class AppManager
 * @package AppManager\Core\Facades
 * @method static registerRoutes
 * @method static getLoadedPackages
 * @method static getLoadedPackagesName
 * @method static isPackageLoaded(string $class)
 */
class Manager extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return \AppManager\Core\Manager::class;
    }
}
