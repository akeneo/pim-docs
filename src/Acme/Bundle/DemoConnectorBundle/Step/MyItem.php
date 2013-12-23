<?php 

namespace Acme\Bundle\DemoConnectorBundle\Step;

use Oro\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;

class MyItem extends AbstractConfigurableStepElement
{
    protected $filePath = '/tmp/truc.csv';

    /**
     * Set the file path
     *
     * @param string $filePath
     *
     * @return FileWriter
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get the file path
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'pim_import_export.export.filePath.label',
                    'help'  => 'pim_import_export.export.filePath.help'
                )
            )
        );
    }

}
