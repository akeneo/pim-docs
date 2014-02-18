<?php
namespace Pim\Bundle\IcecatDemoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Imports data for the demo
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ImportCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('pim:icecat-demo:import')
            ->setDescription('Imports data for the demo');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getContainer()->get('kernel');
        $console = escapeshellarg($this->getPhp()) . ' ' . escapeshellarg($kernel->getRootDir() . '/console');
        $env     = $kernel->getEnvironment();
        foreach ($this->getOrderedJobInstanceCodes() as $jobInstanceCode) {
            $process = new Process($console . ' oro:batch:job ' . $jobInstanceCode . ' --env ' . $env);
            $process->setTimeout(null);
            $process->run();
            $output->writeln(sprintf('<info>Job %s has been successfully executed</info>', $jobInstanceCode));
        }
        $output->writeln('<info>Done</info>');
    }
    protected function getOrderedJobInstanceCodes()
    {
        $jobInstanceCodes = array();
        $productFiles = glob(sprintf('%s/*.csv', $this->getContainer()->getParameter('pim_icecatdemo.import_dir')));
        foreach ($productFiles as $file) {
            $jobInstanceCodes[] = sprintf('initial_product_import_%s', basename($file, '.csv'));
        }

        return $jobInstanceCodes;
    }
    private function getPhp()
    {
        $phpFinder = new PhpExecutableFinder();
        if (!$phpPath = $phpFinder->find()) {
            throw new \RuntimeException('The php executable could not be found, add it to your PATH environment variable and try again');
        }

        return $phpPath;
    }
}
