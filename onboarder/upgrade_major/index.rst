How to upgrade to a major version
=================================

New major versions bring compatibility with new Akeneo PIM releases. So the only case you'll have to perform a major
update of the Onboarder bundle is when you are migrating the PIM itself to a new version.

The instructions you will follow to update your PIM are mostly enough to migrate the bundle, you will only need to do a
couple more things specific to the Onboarder bundle.


Right before updating you composer dependencies
-----------------------------------------------

When updating the PIM ``composer.json`` to increase the PIM version, you also need to change the
``akeneo/pim-onboarder`` version. Please refer to :doc:`/onboarder/installation/index` to know which version of the
bundle to use.

Then, make sure the Onboarder composer script is present in the ``composer.json`` file, as explained in :doc:`/onboarder/installation/index`.

Right after updating you composer dependencies
----------------------------------------------

Thanks to the Onboarder composer script, all the migration scripts of the Onboarder bundle should have been copied
in the ``upgrades/schema`` directory, alongside those of the PIM. Before executing the migrations (both those of the PIM
and those of the Onboarder bundle), please make sure to follow the additional instructions of the ``UPGRADE.md`` file
contained in the bundle (``vendor/akeneo/pim-onboarder/UPGRADE.md``). It will provide you accurate instructions on how
to run the migration scripts.

You can then follow the rest of the PIM upgrade instructions.
