<?php

namespace Bundle\AzureBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AzureBundle extends Bundle
{
    public function registerExtensions(ContainerBuilder $container)
    {
        parent::registerExtensions($container);

        // @todo temporary hack to overcome FrameworkExtension limitation
        $container->setParameter('session.storage.azure.options', array());
    }
}
