<?php

namespace LemoBase\View\Helper;

use Zend\Mvc\Router\RouteMatch as RouteMatchAbc;
use Zend\View\Helper\AbstractHelper;

class RouteMatch extends AbstractHelper
{
    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * __invoke
     *
     * @return RouteMatchAbc
     */
    public function __invoke()
    {
        return $this->routeMatch;
    }

    /**
     * Set route match
     *
     * @param  RouteMatchAbc $routeMatch
     * @return RouteMatch
     */
    public function setRouteMatch(RouteMatchAbc $routeMatch)
    {
        $this->routeMatch = $routeMatch;
        return $this;
    }
}
