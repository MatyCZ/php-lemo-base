<?php

namespace LemoBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\Controller\PluginManager;
use Zend\View\HelperPluginManager;

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
            'factories' => [
                'LemoBase\Mvc\Controller\Plugin\CacheManager' => 'LemoBase\Mvc\Controller\Plugin\CacheManagerFactory',
                'LemoBase\Mvc\Controller\Plugin\Notice' => function (PluginManager $pluginManager) {
                    $plugin = new Mvc\Controller\Plugin\Notice();
                    return $plugin;
                },
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
                'LemoBase\Cache\CacheManager'  => 'LemoBase\Cache\CacheManagerFactory',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'paramsQuery'    => 'LemoBase\View\Helper\ParamsQuery',
                'notice'         => 'LemoBase\View\Helper\Notice',
                'lang'           => 'LemoBase\View\Helper\Lang',
            ],
            'factories' => [
                'cachemanager' => function(HelperPluginManager $helperPluginManager) {
                    $helper = new View\Helper\CacheManager();
                    $helper->setPluginCacheManager($helperPluginManager->getServiceLocator()->get('ControllerPluginManager')->get('cachemanager'));
                    return $helper;
                },
                'routeMatch' => function(HelperPluginManager $helperPluginManager) {
                    $match = $helperPluginManager->getServiceLocator()
                        ->get('application')
                        ->getMvcEvent()
                        ->getRouteMatch();

                    $helper = new View\Helper\RouteMatch();

                    if (null !== $match) {
                        $helper->setRouteMatch($match);
                    }

                    return $helper;
                },
            ]
        ];
    }
}
