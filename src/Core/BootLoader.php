<?php

namespace AppManager\Core;

use Illuminate\Support\Facades\Route;
use AppManager\Core\Interfaces\BootLoaderInterface;

abstract class BootLoader
{
    use BootLoaderInterface;

    /**
     * The package is Essential
     */
    const PACKAGE_ESSENTIAL = 'essential';

    /**
     * The package is optional
     */
    const PACKAGE_OPTIONAL = 'optional';

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $options = [];

        $RouteNameSlug = $this->routeName() ?? '';
        if (isset($this->mainRouteNameSlug) && !empty($this->mainRouteNameSlug))
            $RouteNameSlug = $this->mainRouteNameSlug . '.' . $RouteNameSlug;
        if (isset($RouteNameSlug) && !empty($RouteNameSlug) && !str_ends_with($RouteNameSlug, '.'))
            $RouteNameSlug = "$RouteNameSlug.";
        if (isset($RouteNameSlug) && !empty($RouteNameSlug))
            $options['as'] = $RouteNameSlug;

        $RoutePrefix = $this->routePrefix() ?? '';
        if (isset($this->mainRoutePrefix) && !empty($this->mainRoutePrefix))
            $RoutePrefix = trim($this->mainRoutePrefix, '/') . '/' . trim($RoutePrefix, '/');
        if (isset($RoutePrefix) && !empty($RoutePrefix))
            $options['prefix'] = trim($RoutePrefix, '/');

        $routeMiddleware = array_merge($this->mainRouteMiddleware ?? [], $this->routeMiddleware() ?? []);
        if (count($routeMiddleware) > 0)
            $options['middleware'] = $routeMiddleware;

        Route::group($options, function () {
            $this->registerRouters();
        });
        $this->registerViewLocations();
    }

    /**
     * Define The routes Middleware.
     * @return array
     */
    public function routeMiddleware()
    {
        return [];
    }

    /**
     * Define The routes name slug.
     * @return string
     */
    public function routePrefix()
    {
        return '';
    }

    /**
     * Define The package routes.
     * @return void
     */
    abstract public function registerRouters();

    /**
     * Register any custom view locations if available
     */
    protected function registerViewLocations()
    {
        if (empty($this->viewLocations)) {
            return;
        }

        foreach ($this->viewLocations as $viewLocation) {
            view()->addNamespace(key($viewLocation), $viewLocation[key($viewLocation)]);
        }

        $this->manager->event("Custom views registered for " . get_called_class(), $this->viewLocations);
    }
}
