<?php

namespace Lemo\Base\View\Helper;

use Laminas\Router\RouteMatch as LaminasRouteMatch;
use Laminas\View\Helper\AbstractHelper;

class RouteMatch extends AbstractHelper
{
    public function __construct(
        private ?LaminasRouteMatch $routeMatch
    ) {
    }

    public function __invoke(): ?LaminasRouteMatch
    {
        return $this->routeMatch;
    }
}
