<?php

namespace Lemo\Base\View\Helper;

use Laminas\Mvc\Controller\PluginManager as ControllerPluginManager;
use Psr\Container\ContainerInterface;

class NoticeFactory
{
    public function __invoke(ContainerInterface $container): Notice
    {
        return new Notice(
            $container->get(ControllerPluginManager::class)->get('notice')
        );
    }
}
