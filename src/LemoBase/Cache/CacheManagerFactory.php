<?php

namespace LemoBase\Cache;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class CacheManagerFactory implements FactoryInterface
{
    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return CacheManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $request = $container->get('Request');

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

    /**
     * Create an object (v2)
     *
     * @param  ServiceLocatorInterface $container
     * @return CacheManager
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, CacheManager::class);
    }
}
