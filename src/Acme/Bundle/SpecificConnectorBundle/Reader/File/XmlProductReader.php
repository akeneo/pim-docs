<?php

namespace Acme\Bundle\SpecificConnectorBundle\Reader\File;

use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Pim\Bundle\BaseConnectorBundle\Reader\File\FileReader;

class XmlProductReader extends FileReader implements ItemReaderInterface
{
    protected $xml;

    public function read()
    {
        if (null === $this->xml) {
            // for exemple purpose, we should use XML Parser to read line per line
            $this->xml = simplexml_load_file($this->filePath, 'SimpleXMLIterator');
            $this->xml->rewind();
        }

        if ($data = $this->xml->current()) {
            $this->xml->next();

            return $data;
        }

        return null;
    }

    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'acme_specific_connector.steps.import.filePath.label',
                    'help'  => 'acme_specific_connector.steps.import.filePath.help'
                )
            ),
        );
    }
}
