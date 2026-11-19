How to Avoid Rules Execution on Mass Edit Actions
=================================================

| The Akeneo PIM comes with a number of mass edit actions.
| By default in Enterprise Edition, after each of those actions, **rules are applied** for updated products.
| Please follow this cookbook if you don't want rules to apply for some reason.

Quick Overview
--------------

.. note::

  This cookbook is about a feature only provided in the **Enterprise Edition**.

This cookbook assumes that you already created a new bundle to add your custom rule. Let's assume its namespace is `Acme\\CustomBundle`.

One Service to Override
-----------------------

| For this cookbook let say we **don't want to apply rules** after a Mass Edit Common Attributes operation.
| Currently, this operation is defined as follows:

.. code-block:: php

        # src/PimEnterprise/Bundle/EnrichBundle/Resources/config/mass_actions.yml

        pim_enrich.mass_edit_action.edit_common_attributes:
            public: false
            class: %pim_enrich.mass_edit_action.edit_common_attributes.class%
            arguments:
                - '@pim_catalog.builder.product'
                - '@pim_user.context.user'
                - '@pim_catalog.repository.attribute'
                - '@pim_catalog.updater.product'
                - '@pim_catalog.validator.product'
                - '@pim_internal_api_serializer'
                - '@pim_catalog.localization.localizer.converter'
                - '@pim_catalog.localization.localizer.registry'
                - '@pim_enrich.filter.product_values_edit_data'
                - '%tmp_storage_dir%'
                - 'edit_common_attributes_with_permission_and_rules'        # <<<< The batch job code that will run in background
            tags:
                -
                    name: pim_enrich.mass_edit_action
                    alias: edit-common-attributes
                    acl: pim_enrich_product_edit_attributes
                    datagrid: product-grid

| As you can see in the service declaration above, by default in Enterprise Edition, the job used to Edit Common Attributes is ``edit_common_attributes_with_permission_and_rules``.
| This job has multiple steps, including the rule execution step.

| All you have to do is to redefine this service and inject another existing job code: ``edit_common_attributes_with_permission``.
| This job is part of the minimal data fixtures and is already in database. Here is what it looks like:

.. code-block:: php

        # src/Acme/Bundle/CustomBundle/Resources/config/mass_actions.yml

        pim_enrich.mass_edit_action.edit_common_attributes:
            public: false
            class: %pim_enrich.mass_edit_action.edit_common_attributes.class%
            arguments:
                - '@pim_catalog.builder.product'
                - '@pim_user.context.user'
                - '@pim_catalog.repository.attribute'
                - '@pim_catalog.updater.product'
                - '@pim_catalog.validator.product'
                - '@pim_internal_api_serializer'
                - '@pim_catalog.localization.localizer.converter'
                - '@pim_catalog.localization.localizer.registry'
                - '@pim_enrich.filter.product_values_edit_data'
                - '%tmp_storage_dir%'
                - 'edit_common_attributes_with_permission'        # <<<< Notice we do not use rules anymore with this job code
            tags:
                -
                    name: pim_enrich.mass_edit_action
                    alias: edit-common-attributes
                    acl: pim_enrich_product_edit_attributes
                    datagrid: product-grid


Available Mass Edit Operation Jobs Without Rules
------------------------------------------------

| The previous example was for the Mass Edit Common Attributes operation. If you want to customize another operation, please proceed the exact same way but by using one of these jobs.
| Here are the mass edit jobs that can be ran without rules execution:

**Classify**, **Add to Groups**:

- ``add_product_value``: Add product value (CE default)
- ``add_product_value_with_permission``: Add product value with EE permission check (EE Only)
- ``add_product_value_with_permission_and_rules``: Add product value with EE permission check & rules application (EE Only, EE default)

**Change Status**, **Change Family**:

- ``update_product_value``: Update product value (CE default)
- ``update_product_value_with_permission``: Update product value with EE permission check (EE Only)
- ``update_product_value_with_permission_and_rules``: Update product value with EE permission check & rules application (EE Only, EE default)

**Edit Common Attributes**:

- ``edit_common_attributes``: Edit common attributes (CE Default)
- ``edit_common_attributes_with_permission``: Edit common attributes with EE permission check (EE Only)
- ``edit_common_attributes_with_permission_and_rules``: Edit common attributes with EE permission check & rules application (EE Only, EE Default)

.. note::

  All mass edit operation jobs are in the minimal data fixtures and declared in ``src/PimEnterprise/Bundle/InstallerBundle/Resources/fixtures/minimal/jobs.yml``
