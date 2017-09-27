<?php

namespace Acme\Bundle\XmlConnectorBundle\Job\JobParameters;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use Akeneo\Component\Localization\Localizer\LocalizerInterface;
use Pim\Component\Catalog\Validator\Constraints\FileExtension;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class SimpleXmlImport implements
    DefaultValuesProviderInterface,
    ConstraintCollectionProviderInterface
{
    /** @var ConstraintCollectionProviderInterface */
    protected $constraintCollectionProvider;

    /** @var DefaultValuesProviderInterface */
    protected $defaultValuesProvider;

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        return [
            'filePath'                  => null,
            'uploadAllowed'             => true,
            'dateFormat'                => LocalizerInterface::DEFAULT_DATE_FORMAT,
            'decimalSeparator'          => LocalizerInterface::DEFAULT_DECIMAL_SEPARATOR,
            'enabled'                   => true,
            'categoriesColumn'          => 'categories',
            'familyColumn'              => 'family',
            'groupsColumn'              => 'groups',
            'enabledComparison'         => true,
            'realTimeVersioning'        => true,
            'invalid_items_file_format' => 'xml',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintCollection()
    {
        return new Collection(
            [
                'fields' => [
                    'filePath' => [
                        new NotBlank(['groups' => ['Execution', 'UploadExecution']]),
                        new FileExtension(
                            [
                                'allowedExtensions' => ['xml', 'zip'],
                                'groups'            => ['Execution', 'UploadExecution']
                            ]
                        )
                    ],
                    'uploadAllowed' => [
                        new Type('bool'),
                        new IsTrue(['groups' => 'UploadExecution']),
                    ],
                    'decimalSeparator' => [
                        new NotBlank()
                    ],
                    'dateFormat' => [
                        new NotBlank()
                    ],
                    'enabled' => [
                        new Type('bool')
                    ],
                    'categoriesColumn' => [
                        new NotBlank()
                    ],
                    'familyColumn' => [
                        new NotBlank()
                    ],
                    'groupsColumn' => [
                        new NotBlank()
                    ],
                    'enabledComparison' => [
                        new Type('bool')
                    ],
                    'realTimeVersioning' => [
                        new Type('bool')
                    ],
                    'invalid_items_file_format' => [
                        new Type('string')
                    ]
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(JobInterface $job)
    {
        return $job->getName() === 'xml_product_import';
    }
}
