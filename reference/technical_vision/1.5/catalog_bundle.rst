Catalog Bundle & Component [WIP]
================================

We've extracted following classes and interfaces from the Catalog bundle to the Catalog component:
 - model interfaces and classes as ProductInterface
 - repository interfaces as ProductRepositoryInterface
 - builder interfaces as ProductBuilderInterface

In v1.4, we've re-worked the file storage system, the model `Pim\Component\Catalog\Model\ProductMediaInterface` is not used anymore, we now use `Akeneo\Component\FileStorage\Model\FileInfoInterface`.

In v1.5, we've removed following deprecated classes, interfaces and services:
 - `Pim\Component\Catalog\Model\ProductMediaInterface`
 - `Pim\Component\Catalog\Model\AbstractProductMedia`
 - `Pim\Component\Catalog\Model\ProductMedia`
 - `Pim\Bundle\CatalogBundle\Factory\MediaFactory` and `@pim_catalog.factory.media`
 - `Pim\Bundle\CatalogBundle\MongoDB\Normalizer\MediaNormalizer`
 - `Pim\Bundle\TransformBundle\Normalizer\MongoDB\ProductMediaNormalizer`
 - `PimEnterprise\Bundle\WorkflowBundle\DependencyInjection\Compiler\RegisterProductValuePresentersPass`
 - `PimEnterprise\Bundle\WorkflowBundle\Presenter\ProductValue\BooleanPresenter`
 - `PimEnterprise\Bundle\WorkflowBundle\Presenter\ProductValue\DatePresenter`
 - `PimEnterprise\Bundle\WorkflowBundle\Presenter\ProductValue\FilePresenter`
 - `PimEnterprise\Bundle\WorkflowBundle\Presenter\ProductValue\ImagePresenter`
 - `PimEnterprise\Bundle\WorkflowBundle\Presenter\ProductValue\ProductValuePresenterInterface`
 - `PimEnterprise\Bundle\WorkflowBundle\Twig\ProductValuePresenterExtension`

We've also removed following requirements from composer.json, you can do the same in your project:

```
    "knplabs/gaufrette": "0.1.9",
    "knplabs/knp-gaufrette-bundle": "0.1.7"
```

As usual, we provide upgrade commands (cf last chapter) to easily update projects migrating from 1.4 to 1.5.

Don't forget to change the app/config.yml if you did mapping overrides:

v1.4
```
akeneo_storage_utils:
    mapping_overrides:
        -
            original: Pim\Bundle\CatalogBundle\Model\ProductValue
            override: Acme\Bundle\AppBundle\Model\ProductValue
```

v1.5
```
akeneo_storage_utils:
    mapping_overrides:
        -
            original: Pim\Component\Catalog\Model\ProductValue
            override: Acme\Bundle\AppBundle\Model\ProductValue
```
