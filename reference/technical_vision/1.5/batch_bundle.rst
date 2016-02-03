Batch Bundle & Component
========================

The Akeneo/BatchBundle has been introduced in the very first version of the PIM.

It resides in a dedicated Github repository and, due to that, it has not been enhanced as much as the other bundles.

It's a shame because this bundle provides the main interfaces and classes to structure the connectors for import/export.

To ease the improvements of this key part of the PIM, we moved the bundle in the pim-community-dev repository.

With the same strategy than for other old bundles, main technical interfaces and classes are extracted in a Akeneo/Batch component.

It helps to clearly separate its business logic and the Symfony and Doctrine "glue".

Has been done:
 - move BatchBundle to pim-community-dev repository
 - extract main Step interface and classes
 - extract main Item interface and classes
 - extract main exceptions
 - extract main Event interface and classes
 - extract main Job interface and classes
 - extract domain models (doctrine entities) and move doctrine mapping to yml files
 - extract annotation validation in yml files (move also existing constraint from ImportExportBundle)
 - replace unit tests by specs, add missing specs
 - remove useless batch bundle files (composer, readme, upgrade, travis setup, etc)

After this re-work, several batch domain classes remain in the BatchBundle.

Several of these classes are deprecated, several others are not even used in the context of the PIM (we need extra analysis to know what to do with these).

Another remaining issue with the Batch component is the mix of concerns, batch logic, job and step configuration and step element UI configuration.

The Akeneo\Component\Batch\Step\StepInterface should not contain getConfiguration(), setConfiguration() and getConfigurableStepElements().

The Akeneo\Component\Batch\Step\StepInterface should not assume the use of Akeneo\Component\Batch\Item\AbstractConfigurableStepElement.

Another issue in the Batch Bundle is the way the 'batch_jobs.yml' files are parsed and systematically stored in the DIC.

We could rely on a more standard way to define the batch services.

As usual, we provide upgrade commands (cf last chapter) to easily update projects migrating from 1.4 to 1.5.

During upgrade, you also have to remove the following line from your project composer.json:

```
    "akeneo/batch-bundle": "0.4.5",
```
