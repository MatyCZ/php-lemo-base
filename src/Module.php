<?php

namespace Lemo\Base;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'controller_plugins' => $provider->getControllerPluginsConfig(),
            'view_helpers' => $provider->getViewHelpersConfig(),
        ];
    }
}
