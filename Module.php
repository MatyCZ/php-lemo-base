<?php

namespace LemoBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\View\HelperPluginManager;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ControllerPluginProviderInterface,
    ViewHelperProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
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
        return array(
            'invokables' => array(
                'notice'         => 'LemoBase\Mvc\Controller\Plugin\Notice',
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'paramsQuery'    => 'LemoBase\View\Helper\ParamsQuery',
                'notice'         => 'LemoBase\View\Helper\Notice',
                'lang'           => 'LemoBase\View\Helper\Lang',
            ),
            'factories' => array(
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
            )
        );
    }
}
