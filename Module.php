<?php

namespace LemoBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ControllerPluginProviderInterface, ViewHelperProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
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
                'formRenderRow'  => 'LemoBase\Form\View\Helper\FormRenderRow',
                'formValidator'  => 'LemoBase\Form\View\Helper\FormValidator',
                'paramsQuery'    => 'LemoBase\View\Helper\ParamsQuery',
                'notice'         => 'LemoBase\View\Helper\Notice',
                'lang'           => 'LemoBase\View\Helper\Lang',
            ),
        );
    }
}
