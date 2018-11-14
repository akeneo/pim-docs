How to change the validation rule to match a reference file to an asset
=======================================================================

**This cookbook is about a feature only provided in the Enterprise Edition.**

To add files to assets in Akeneo we use a simple mapping strategy to match a filename to a product asset. As an integrator you can customize this rule to fit your own needs.

Let's say that we only have localizable assets in US english and want to add a suffix to every file we import and remove the 3 first letters of their names.

Override the UploadChecker
--------------------------

First, we will override the UploadChecker to be able to provide our own ParsedFilenameInterface implementation.

.. code-block:: yaml

    parameters:
        pimee_product_asset.upload_checker.class: Acme\CustomBundle\ProductAsset\Upload\UploadChecker


We can now create our own ParsedFilename:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\ProductAsset\Upload;

    use Akeneo\Asset\Component\Upload\UploadChecker as BaseUploadChecker;

    class UploadChecker extends BaseUploadChecker
    {
        /**
         * @param string $filename
         *
         * @return ParsedFilenameInterface
         */
        public function getParsedFilename($filename)
        {
            return new ParsedFilename($this->locales, $filename);
        }
    }

Implement the custom logic
--------------------------

You can now implement your own custom logic in your own ParsedFilenameInterface implementation:

.. code-block:: php

    <?php

    namespace Acme\CustomBundle\ProductAsset\Upload;

    use Akeneo\Asset\Component\Upload\ParsedFilename as BaseParsedFilename;
    use Pim\Bundle\CatalogBundle\Model\LocaleInterface;

    /**
     * @see FilenameParserInterface
     */
    class ParsedFilename extends BaseParsedFilename
    {
        /**
         * @param LocaleInterface[] $availableLocales
         * @param                   $rawFilename
         */
        public function __construct(array $availableLocales, $rawFilename)
        {
            $this->rawFilename      = $rawFilename;
            $this->availableLocales = $availableLocales;

            $this->parseRawFilename($this->rawFilename);
        }

        /**
         * {@inheritdoc}
         */
        public function parseRawFilename($rawFilename)
        {
            if (preg_match('/^(?P<name>[0-9a-z-_]+)\.(?P<extension>[0-9a-z]+)$/i', $rawFilename, $matches)) {
                $customName = substr($matches['name'], 3) . '_custom_suffix';

                $this->assetCode  = strlen($matches['name']) > 0 ? $customName : null;
                $this->localeCode = 'en_US';
                $this->extension  = $matches['extension'];
            }
        }
    }

You can now imagine implementing your own logic and customize this rule as you want.
