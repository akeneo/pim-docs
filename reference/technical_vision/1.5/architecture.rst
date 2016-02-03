Architecture [WIP]
==================

Once Oro bundles moved, there is the re-work strategy for v1.5.

The idea is to provide a cleaner and more understandable stack by removing override and "twin bundles".

There is the v1.4 version with Oro bundles moved in src,

```
src/
├── Acme
│   └── Bundle
│       └── AppBundle
├── Akeneo
│   ├── Bundle
│   │   ├── ClassificationBundle
│   │   ├── FileStorageBundle
│   │   └── StorageUtilsBundle
│   └── Component
│       ├── Analytics
│       ├── Classification
│       ├── Console
│       ├── FileStorage
│       └── StorageUtils
├── Oro
│   └── Bundle
│       ├── AsseticBundle           -> -
│       ├── ConfigBundle            -> -
│       ├── DataGridBundle          -> -
│       ├── EntityBundle            -> [Done] - removed (DoctrineOrmMappingsPass has been extracted in Akeneo/StorageUtilsBundle)
│       ├── EntityConfigBundle      -> [Done] - removed (ServiceLinkPass has been extracted in Oro/SecurityBundle)
│       ├── DistributionBundle      -> [Done] - removed (automatic routing has been dropped and routes are explicitly declared in routing.yml)
│       ├── FilterBundle            -> -
│       ├── FormBundle              -> merge useful parts to Oro/ConfigBundle and Pim/EnrichBundle
│       ├── LocaleBundle            -> [Done] - removed
│       ├── NavigationBundle        -> -
│       ├── RequireJSBundle         -> -
│       ├── SecurityBundle          -> -
│       ├── TranslationBundle       -> merge useful parts to Akeneo/Pim Localization then remove
│       ├── UIBundle                -> merge useful parts to Pim/UIBundle
│       └── UserBundle              -> merge useful parts to Pim/UserBundle
└── Pim
    ├── Bundle
    │   ├── AnalyticsBundle         -> -
    │   ├── BaseConnectorBundle     -> could be totally deprecated (but kept with tests) once exports re-worked in ConnectorBundle
    │   ├── CatalogBundle           -> continue to extract business code to relevant components
    │   ├── CommentBundle           -> could be splitted in a Akeneo component + bundle (does not rely on PIM domain)
    │   ├── ConnectorBundle         -> could welcome new classes if we re-work export
    │   ├── DashboardBundle         -> -
    │   ├── DataGridBundle          -> move generic classes to Oro/DataGridBundle, move specific related to product to Pim/EnrichBundle
    │   ├── EnrichBundle            -> could contain all Akeneo PIM UI (except independent bundles as workflow, pam)
    │   ├── FilterBundle            -> merge in Oro/DataGridBundle or Pim/DataGridBundle
    │   ├── ImportExportBundle      -> could be merged to EnrichBundle it mainly contain UI related classes
    │   ├── InstallerBundle         -> -
    │   ├── JsFormValidationBundle  -> -
    │   ├── NavigationBundle        -> -
    │   ├── NotificationBundle      -> bit re-worked during the collaborative workflow epic
    │   ├── PdfGeneratorBundle      -> -
    │   ├── ReferenceDataBundle     -> -
    │   ├── TransformBundle         -> move normalizer/denormalizer part and deprecate all other parts (related to deprecated import system)
    │   ├── TranslationBundle       -> copy useful classes in new Localization component + bundle, then remove this bundle
    │   ├── UIBundle                -> mainly used for js/css third party libraries, we should load them via a dedicated package manager
    │   ├── UserBundle              -> merge used parts of Oro/UserBundle to Pim/UserBundle
    │   ├── VersioningBundle        -> -
    │   └── WebServiceBundle        -> -
    └── Component
        ├── Catalog
        ├── Connector
        └── ReferenceData
```

Ideally, the 1.5 version could be the following, (depending on the amount of tech cleaning we manage to do),

```
src/
├── Acme
│   └── Bundle
│       └── AppBundle               Dev examples for product value override and specific reference data
├── Akeneo
│   ├── Bundle
│   │   ├── BatchBundle             Doctrine and Symfony implementations for the Batch component
│   │   ├── ClassificationBundle    Doctrine generic implementations for classification trees and related DI
│   │   ├── FileStorageBundle       Doctrine and Symfony implementations for files storage
│   │   └── StorageUtilsBundle      Doctrine implementations for storage access (remover, saver, updater, repositories, etc)
│   └── Component
│       ├── Analytics               Data collector interfaces to aggregate statistics
│       ├── Batch                   New (introduced v1.5) Batch domain interfaces and classes (extracted from previous BatchBundle version)
│       ├── Buffer                  New (introduced v1.5) Buffer domain interfaces and classes (extracted from previous BatchBundle version)
│       ├── Classification          Generic classes for classification trees (implemented by product categories and asset categories) and tags
│       ├── Console                 Utility classes to execute commands
│       ├── FileStorage             Business interfaces and classes to handle files storage with filesystem abstraction
│       ├── Localization            New (introduced v1.5) Localization domain interfaces and classes
│       ├── StorageUtils            Business interfaces and classes to abstract storage access (remover, saver, updater, repositories, etc)
│       └── Versioning              New (introduced v1.5) Versioning domain interfaces and classes
├── Oro
│   └── Bundle
│       ├── AsseticBundle           CSS assets management, assets can be distributed across several bundles
│       ├── ConfigBundle            Application configuration, other bundles can declare their own configurations
│       ├── DataGridBundle          Generic interfaces and classes to implement Datagrid
│       ├── FilterBundle            Generic interfaces and classes to implement Datagrid filters
│       ├── FormBundle              Form utils
│       ├── RequireJSBundle         Generates a require.js config file for a project, minify and merge all JS-file into one resources
│       └── SecurityBundle          Advanced ACL management
└── Pim
    ├── Bundle
    │   ├── AnalyticsBundle         Implementations of data collectors to provide PIM statistics
    │   ├── CatalogBundle           PIM business classes (models, model updaters, storage access, validation, etc)
    │   ├── CommentBundle           Generic comment implementations, used by products
    │   ├── ConnectorBundle         New (introduced in v1.5) classes to integrate import system with Symfony and Doctrine
    │   ├── DashboardBundle         Dashboard and widget system
    │   ├── EnrichBundle            Symfony and Doctrine glue classes to provide User Interface
    │   ├── InstallerBundle         Installation system of the PIM
    │   ├── JsFormValidationBundle  Override of APY/JsFormValidationBundle to provide javascript validation for dynamic models
    │   ├── LocalizationBundle      Symfony implementation of localization features
    │   ├── NotificationBundle      Implementation of a centralized PIM notifications system
    │   ├── PdfGeneratorBundle      Classes to generate a PDF datasheet for a product
    │   ├── ReferenceDataBundle     Classes to provide reference data support for PIM features
    │   ├── TransformBundle         Handles normalization and denormalization of PIM models
    │   ├── UserBundle              Interfaces and classes to manage Users, Roles and Groups
    │   ├── VersioningBundle        Versioning implementation for the PIM domain models
    │   └── WebServiceBundle        Very light Web Rest API (json format)
    └── Component
        ├── Catalog                 New (introduced v1.4) PIM domain interfaces and classes, most of them still remain in CatalogBundle for legacy reasons
        ├── Connector               New (introduced v1.4) PIM business interfaces and classes to handle data import
        └── ReferenceData           New (introduced v1.4) Interfaces and classes related to collection of reference models and the product integration
```
