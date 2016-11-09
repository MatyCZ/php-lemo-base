<?php

namespace LemoBase;

return [

    // CONTROLLER PLUGINS
    'controller_plugins' => [
        'aliases' => [
            'notice' => Mvc\Plugin\Notice::class,
        ],
        'invokables' => [
            Mvc\Plugin\Notice::class => Mvc\Plugin\Notice::class,
        ],
    ],

    // SERVICE MANAGER
    'service_manager' => [
        'aliases' => [
            'CacheManager' => Cache\CacheManager::class,
        ],
        'factories' => [
            Cache\CacheManager::class => Cache\CacheManagerFactory::class,
        ]
    ],

    // VIEW HELPERS
    'view_helpers' => [
        'aliases' => [
            'notice'     => View\Helper\Notice::class,
            'routeMatch' => View\Helper\RouteMatch::class,
        ],
        'invokables' => [
            'paramsQuery' => View\Helper\ParamsQuery::class,
        ],
        'factories' => [
            View\Helper\Notice::class     => View\Helper\NoticeFactory::class,
            View\Helper\RouteMatch::class => View\Helper\RouteMatchFactory::class,
        ],
    ],
];
