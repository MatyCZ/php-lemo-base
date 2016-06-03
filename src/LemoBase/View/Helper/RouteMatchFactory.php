<?php

namespace LemoBase\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouteMatchFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return RouteMatch
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $match = $container
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();

        $routeMatch = new RouteMatch();

        if (null !== $match) {
            $routeMatch->setRouteMatch($match);
        }

        return $routeMatch;
    }

    /**
     * Create an object (v2)
     *
     * @param  ServiceLocatorInterface $container
     * @return RouteMatch
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, RouteMatch::class);
    }
}
