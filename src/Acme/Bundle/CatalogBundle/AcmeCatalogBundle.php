<?php

namespace Acme\Bundle\CatalogBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeCatalogBundle extends Bundle
{
    public function getParent()
    {
        return 'PimCatalogBundle';
    }
}
