<?php

namespace Lemo\Base\View\Helper;

use Psr\Container\ContainerInterface;

class RouteMatchFactory
{
    public function __invoke(ContainerInterface $container): RouteMatch
    {
        $match = $container
            ->get('application')
            ->getMvcEvent()
            ->getRouteMatch();

        return new RouteMatch(
            $match
        );
    }
}
