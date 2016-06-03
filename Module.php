<?php

namespace LemoBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ControllerPluginProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getConfig($env = null)
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @inheritdoc
     */
    public function getControllerPluginConfig()
    {
        return [
            'aliases' => [
                'cachemanager' => 'LemoBase\Mvc\Controller\Plugin\CacheManager',
                'notice'       => 'LemoBase\Mvc\Controller\Plugin\Notice',
            ],
            'invokables' => [
                'LemoBase\Mvc\Controller\Plugin\Notice' => 'LemoBase\Mvc\Controller\Plugin\Notice',
            ],
            'factories' => [
                'LemoBase\Mvc\Controller\Plugin\CacheManager' => 'LemoBase\Mvc\Controller\Plugin\CacheManagerFactory',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getServiceConfig()
    {
        return [
            'aliases' => [
                'CacheManager' => 'LemoBase\Cache\CacheManager',
            ],
            'factories' => [
                'LemoBase\Cache\CacheManager' => 'LemoBase\Cache\CacheManagerFactory',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getViewHelperConfig()
    {
        return [
            'aliases' => [
                'routeMatch'  => 'LemoBase\View\Helper\RouteMatch',
            ],
            'invokables' => [
                'notice'      => 'LemoBase\View\Helper\Notice',
                'paramsQuery' => 'LemoBase\View\Helper\ParamsQuery',
            ],
            'factories' => [
                'LemoBase\View\Helper\RouteMatch' => 'LemoBase\View\Helper\RouteMatchFactory',
            ]
        ];
    }
}
