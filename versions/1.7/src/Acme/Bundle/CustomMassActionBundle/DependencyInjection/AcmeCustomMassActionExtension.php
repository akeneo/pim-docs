<?php

namespace Acme\Bundle\CustomMassActionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

class AcmeCustomMassActionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('form_types.yml');
        $loader->load('job_parameters.yml');
        $loader->load('jobs.yml');
        $loader->load('mass_actions.yml');
        $loader->load('processors.yml');
        $loader->load('steps.yml');
    }
}
