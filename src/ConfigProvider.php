<?php

namespace Lemo\Base;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'controller_plugins' => $this->getControllerPluginsConfig(),
            'view_helpers' => $this->getViewHelpersConfig(),
        ];
    }

    public function getControllerPluginsConfig(): array
    {
        return [
            'aliases' => [
                'notice' => Mvc\Plugin\Notice::class,
            ],
            'invokables' => [
                Mvc\Plugin\Notice::class => Mvc\Plugin\Notice::class,
            ],
        ];
    }

    public function getViewHelpersConfig(): array
    {
        return [
            'aliases' => [
                'notice' => View\Helper\Notice::class,
                'routeMatch' => View\Helper\RouteMatch::class,
            ],
            'invokables' => [
                'paramsQuery' => View\Helper\ParamsQuery::class,
            ],
            'factories' => [
                View\Helper\Notice::class => View\Helper\NoticeFactory::class,
                View\Helper\RouteMatch::class => View\Helper\RouteMatchFactory::class,
            ],
        ];
    }
}
