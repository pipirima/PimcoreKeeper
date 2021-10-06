<?php

namespace Pipirima\PimcoreKeeperBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class PimcoreKeeperExtension
 * @package Pipirima\PimcoreKeeperBundle\DependencyInjection
 */
class PimcoreKeeperExtension extends Extension
{
    /**
     * @inheritdoc
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter($this->getAlias() . '.alerts', $config['alerts']);
        $container->setParameter($this->getAlias() . '.debug', $config['debug']);
    }
}
