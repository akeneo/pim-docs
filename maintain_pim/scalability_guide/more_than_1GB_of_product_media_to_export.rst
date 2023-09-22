More than 1GB of product media to export?
-----------------------------------------

If you run an export with products containing media, an archive is created at the end. It contains the products data files and all the media for these products.
However, trying to archive a large volume of media (usually more than 1GB) can fail because it's a very memory consuming process in PHP.

It is handled by ``Akeneo\Tool\Component\Connector\Archiver\ArchivableFileWriterArchiver`` which internally uses `Flysystem ZipArchive`_. Unfortunately this has not been optimized to work with large volumes.

.. _`Flysystem ZipArchive`: https://github.com/thephpleague/flysystem-ziparchive

Disable the media archiving
===========================

If you encounter memory issues and you don't need media to be archived, a simple solution is to override the declaration of ``pim_connector.archiver.archivable_file_writer_archiver`` service as follows:

.. code-block:: yaml

    pim_connector.archiver.archivable_file_writer_archiver:
        class: '%pim_connector.archiver.archivable_file_writer_archiver.class%'
        arguments:
            - '@pim_connector.factory.zip_filesystem'
            - '@oneup_flysystem.archivist_filesystem'
            - '@akeneo_batch.job.job_registry'

The tag ``pim_connector.archiver`` is removed here to prevent the archiver from being used by the Symfony DI. The media archiving will be disabled.

Write your own archiver
=======================

However, you may need to archive media and/or keep the archiving active for other usages.
There is currently no out of the box solution to archive large volumes of media on a classic PIM installation (i.e. with the recommended PHP memory limit configuration).
If you want to write your own archiving logic, you can either override the native archiver or create a new one.

An archiver needs to :

    - implement ``Akeneo\Tool\Component\Connector\Archiver\ArchiverInterface``
    - be declared as a service and be tagged with ``pim_connector.archiver``

Feel free to share it with the community!

Here is an example of a working customization of the native archiver based on the Unix zip command and the Symfony `Process Component`_ :

.. _`Process Component`: https://symfony.com/doc/5.4/components/process.html

.. code-block:: php

    use Akeneo\Tool\Component\Connector\Archiver\ArchivableFileWriterArchiver;
    use Symfony\Component\Process\Process;

    class CustomArchiver extends ArchivableFileWriterArchiver
    {
        public function archive(JobExecution $jobExecution)
        {
            foreach ($jobExecution->getJobInstance()->getJob()->getSteps() as $step) {
                if (!$step instanceof ItemStep) {
                    continue;
                }
                $writer = $step->getWriter();
                if ($this->isWriterUsable($writer)) {
                    $filesystem = $this->getZipFilesystem(
                        $jobExecution,
                        sprintf('%s.zip', pathinfo($writer->getPath(), PATHINFO_FILENAME))
                    );

                    $process = new Process(sprintf(
                        'cd %s && zip -@ %s',
                        dirname($writer->getPath()),
                        $filesystem->getAdapter()->getArchive()->filename
                    ));
                    $process->setStdin(implode(PHP_EOL, $writer->getWrittenFiles()));
                    $process->run();
                }
            }
        }
    }
