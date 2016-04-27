<?php

namespace LemoBase\Cache;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CacheManagerFactory implements FactoryInterface
{
    /**
     * Create and return the view helper manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return CacheManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $request = $serviceLocator->get('Request');

        $cacheManager = new CacheManager($request);

        if (!empty($config['cacheManager']['storages']['factories'])) {
            $cacheManager->setStorages($config['cacheManager']['storages']['factories']);
        }

        if (!empty($config['cacheManager']['storages']['aliases'])) {
            foreach ($config['cacheManager']['storages']['aliases'] as $name => $nameTarget) {
                if (isset($config['cacheManager']['storages']['factories'][$nameTarget])) {
                    $cacheManager->add($config['cacheManager']['storages']['factories'][$nameTarget], $name);
                }
            }
        }

        return $cacheManager;
    }
}
