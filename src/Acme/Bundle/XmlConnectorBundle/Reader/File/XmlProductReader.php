<?php

namespace Acme\Bundle\XmlConnectorBundle\Reader\File;

use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;

class XmlProductReader extends AbstractConfigurableStepElement implements ItemReaderInterface
{
    protected $filePath;

    protected $xml;

    public function read()
    {
        if (null === $this->xml) {
            // for example purpose, we should use XML Parser to read line per line
            $this->xml = simplexml_load_file($this->filePath, 'SimpleXMLIterator');
            $this->xml->rewind();
        }

        if ($data = $this->xml->current()) {
            $item = [];
            foreach ($data->attributes() as $attributeName => $attributeValue) {
                $item[$attributeName] = (string) $attributeValue;
            }
            $this->xml->next();

            return $item;
        }

        return null;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getConfigurationFields()
    {
        return array(
            'filePath' => array(
                'options' => array(
                    'label' => 'acme_xml_connector.steps.import.filePath.label',
                    'help'  => 'acme_xml_connector.steps.import.filePath.help'
                )
            ),
        );
    }
}
