Normalizers & Denormalizers [WIP]
=================================

The PIM heavily uses the Serializer component of Symfony http://symfony.com/doc/2.7/components/serializer.html.

Especially, classes which implement,
 - NormalizerInterface to transform object to array
 - DenormalizerInterface to transform array to object

We have a lot of different formats and for backward compatibility reason, we never re-organized them.

```
Pim/Bundle/CatalogBundle
└── MongoDB
    └── Normalizer          -> format "mongodb_json", used to generate the field normalizedData in a product mongo document

Pim/Bundle/TransformBundle
├── Denormalizer
│   ├── Flat                -> format "flat", "csv", used to revert versions (legacy, it should use structured format + updater api)
│   └── Structured          -> format "json", use to denormalize product templates values
└── Normalizer
    ├── Flat                -> format "flat", "csv", used to generate csv files and versionning format (legacy, it should use structured format)
    ├── MongoDB             -> format "mongodb_document", used to transform a whole object to a MongoDB Document
    └── Structured          -> format "json", "xml", used to generate internal standard format (product template values, product draft values), or for rest api, can also be used with the updater api

Pim/Bundle/EnrichBundle
└── Normalizer              -> format "internal_api", used by the internal rest api to communicate with new UI Forms (product edit form)
```

DONE:

 - The "mongodb_json" format has been moved in Catalog Bundle (not in component because rely on storage classes): Pim/Bundle/CatalogBundle/MongoDB/Normalizer -> Pim/Bundle/CatalogBundle/Normalizer/MongoDB/NormalizedData
 - The parameters and services 'pim_catalog.mongodb.normalizer.*' have been renamed to 'pim_catalog.mongodb.normalizer.normalized_data.*'
 - The "mongodb_document" format has been moved in Catalog Bundle (not in component because rely on storage classes): Pim/Bundle/TransformBundle/Normalizer/MongoDB -> Pim/Bundle/CatalogBundle/Normalizer/MongoDB/Document
 - The parameters and services 'pim_serializer.normalizer.mongodb.*' have been renamed to 'pim_catalog.mongodb.normalizer.document.*'

TODO:

The "structured/json/standard" format could be moved to Catalog component:
 - Pim/Bundle/TransformBundle/Normalizer/Structured -> Pim/Component/Catalog/Normalizer/Structured

The "flat/csv" format should be only used for import/export and could reside in Connector component:
Pim/Bundle/TransformBundle/Normalizer/Flat -> Pim/Component/Connector/Normalizer/Flat
 -> because should only be used for csv import/export (and currently versioning for legacy reasons)

The "internal_api" format could be renamed in Enrich bundle:
 -> Pim/Bundle/EnrichBundle/Normalizer -> Pim/Bundle/EnrichBundle/Normalizer/InternalRest

Other bundles register normalizers/denormalizers for these formats and could be re-organized:

```
├── ImportExportBundle
│   ├── Normalizer
├── ReferenceDataBundle
│   ├── MongoDB
│   ├── Normalizer
├── UserBundle
│   ├── Normalizer
├── LocalizationBundle
│   ├── Normalizer
└── Localization
    ├── Denormalizer
    └── Normalizer
```
