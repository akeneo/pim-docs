<?php

namespace Acme\Bundle\EnrichBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeEnrichBundle extends Bundle
{
    public function getParent()
    {
        return 'PimEnrichBundle';
    }
}
