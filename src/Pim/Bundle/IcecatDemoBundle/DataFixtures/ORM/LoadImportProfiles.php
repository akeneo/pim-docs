<?php

namespace Pim\Bundle\IcecatDemoBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\BatchBundle\Entity\JobInstance;

/**
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class LoadImportProfiles extends ContainerAwareFixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $importDir = $this->container->getParameter('pim_icecatdemo.import_dir');
        $productFiles = glob(sprintf('%s/*.csv', $importDir));
        $registry = $this->getFixtureConfigurationRegistry();
        $file = __DIR__ . '/../../Resources/fixtures/icecat_demo/jobs.yml';
        $reader = $registry->getReader('jobs', 'yml');
        $reader->setFilePath($file);
        while ($data = $reader->read()) {
            if ('csv_product_import' === $data['code']) {
                break;
            }
        }
        $processor = $registry->getProcessor('jobs', 'yml');
        foreach ($productFiles as $file) {
            $data['code'] = 'initial_product_import_' . basename($file, '.csv');
            $data['configuration']['filePath'] = $file;
            $jobInstance = $processor->process($data);
            $manager->persist($jobInstance);
        }
        $manager->flush();
    }

    /**
     * @return \Pim\Bundle\InstallerBundle\FixtureLoader\ConfigurationRegistryInterface
     */
    protected function getFixtureConfigurationRegistry()
    {
        return $this->container->get('pim_installer.fixture_loader.configuration_registry');
    }
}
