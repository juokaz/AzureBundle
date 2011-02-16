<?php

namespace Bundle\AzureBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class AzureExtension extends Extension
{
    public function configLoad($config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__ . '/../Resources/config');
        $loader->load('session.xml');
        $loader->load('cache.xml');
        $loader->load('doctrine.xml');

        if (isset($config['session']) && $config_ = $config['session']) {
            foreach (array('table') as $key) {
                if (!isset($config_[$key]))
                    continue;

                $container->setParameter('azure.session.handler.options.' . $key, $config_[$key]);
            }

            foreach (array('domain', 'account', 'key') as $key) {
                if (!isset($config_[$key]))
                    continue;

                $container->setParameter('azure.session.storage.options.' . $key, $config_[$key]);
            }
        }

        if (isset($config['cache']) && $config_ = $config['cache']) {
            foreach (array('table') as $key) {
                if (!isset($config_[$key]))
                    continue;

                $container->setParameter('azure.cache.options.' . $key, $config_[$key]);
            }
            foreach (array('domain', 'account', 'key') as $key) {
                if (!isset($config_[$key]))
                    continue;

                $container->setParameter('azure.cache.storage.options.' . $key, $config_[$key]);
            }
        }
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    public function getAlias()
    {
        return 'azure';
    }
}