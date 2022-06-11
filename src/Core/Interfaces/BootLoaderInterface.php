<?php

namespace AppManager\Core\Interfaces;

use AppManager\Core\Manager;

trait BootLoaderInterface
{
    /**
     * @var string
     */
    protected $mainRouteNameSlug;

    /**
     * @var string
     */
    protected $mainRoutePrefix;

    /**
     * @var array
     */
    protected $mainRouteMiddleware;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * List of optional custom view locations in the format of :[['Namespace' => __DIR__ . DIRECTORY_SEPARATOR . 'Views'],]
     * @var array
     */
    protected $viewLocations = [];

    /**
     * Get Package's Long name
     * @return string
     */
    abstract public function longName();

    /**
     * Get Package's Short name
     * @return string
     */
    abstract public function shortName();


    /**
     * Get package's Slug
     * @return string
     */
    abstract public function slug();

    /**
     * Get package's Description
     * @return string
     */
    abstract public function description();

    /**
     * Get package's Type Essential, Optional, etc...
     * @return string
     */
    abstract public function type();

    /**
     * Define The routes name slug.
     * @return string
     */
    abstract public function routeName();
}
