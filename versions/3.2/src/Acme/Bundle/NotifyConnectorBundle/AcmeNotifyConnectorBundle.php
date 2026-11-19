<?php

namespace Acme\Bundle\NotifyConnectorBundle;

use Akeneo\Platform\Bundle\ImportExportBundle\DependencyInjection\Compiler\RegisterJobNameVisibilityCheckerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AcmeNotifyConnectorBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new RegisterJobNameVisibilityCheckerPass(
                ['acme_notifyconnector.job_name.csv_product_export_notify']
            ));
    }
}
