<?php

namespace Acme\Bundle\IcecatDemoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class AcmeIcecatDemoExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('attribute_types.yml');
        $loader->load('grid_attribute_types.yml');
        $loader->load('custom_entities.yml');
        $loader->load('form_types.yml');
        $loader->load('orm_filter_types.yml');
        $loader->load('transformers.yml');
        $loader->load('processors.yml');
        $loader->load('entities.yml');
        $loader->load('managers.yml');
    }
}
