<?php

namespace Acme\Bundle\NotifyConnectorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class AcmeNotifyConnectorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('jobs.yml');
        $loader->load('job_parameters.yml');
        $loader->load('job_profile.yml');
        $loader->load('steps.yml');
    }
}
