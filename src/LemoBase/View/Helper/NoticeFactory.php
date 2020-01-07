<?php

namespace LemoBase\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Controller\PluginManager as ControllerPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class NoticeFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return Notice
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Notice(
            $container->get(ControllerPluginManager::class)->get('notice')
        );
    }

    /**
     * Create an object (v2)
     *
     * @param  ServiceLocatorInterface $container
     * @return Notice
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Notice::class);
    }
}
