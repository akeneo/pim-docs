How to adapt the version purger to your needs
=============================================

After an intensive use of the PIM, your database volume may have increased in a way that the PIM holds too many unused versions.

It is possible to purge unused versions in the PIM for a specific entity or for all entities using the version purger (see the command ``pim:versioning:purge``).

By default, the command will purge any version older than 90 days and that is not the first one nor the last one for the entity.

In this cookbook, we will go through each step to customize this command's behavior. It is built to be easily extended to keep versions of your interest.

We are going to customize the version purger to keep versions whose number is pair.

Version Purger's structure
--------------------------

The version purger is able to retrieve the versions from the database and to run them against a list of advisors to know if they need to be purged.

The advisor's responsibility is to indicate whether a version should be purged or not.

- If all advisors registered within the version purger agree on purging, the version will be removed
- If one advisor indicates the version should be kept it is not removed

We can customize the purge process by registering a new advisor within the version purger.


Advisor: SkipPairVersionAdvisor
---------------------------------------

First, we define a new class within our bundle.

.. code-block:: php
    :linenos:

    #/src/Acme/Bundle/AppBundle/VersionPurger/SkipPairVersionAdvisor.php
    <?php

    namespace Acme\Bundle\AppBundle\VersionPurger;

    use Akeneo\Tool\Component\Versioning\Model\VersionInterface;
    use Akeneo\Tool\Bundle\VersioningBundle\Purger\VersionPurgerAdvisorInterface;

    class SkipPairVersionPurgerAdvisor implements VersionPurgerAdvisorInterface
    {
        /**
         * Checks if the advisor supports the version
         *
         * @param VersionInterface $version
         *
         * @return bool
         */
        public function supports(VersionInterface $version)
        {
            // Our advisor supports any kind of versionable entity
            return true;
        }

        /**
         * Indicates if the version needs to be purged
         *
         * @param VersionInterface $version
         * @param array            $options
         *
         * @return bool
         */
        public function isPurgeable(VersionInterface $version, array $options)
        {
            // We implement our business logic here
            // We check the version number to keep only pair versions
            return 0 !== $version->getVersion() % 2;
        }
    }

Now that our advisor is defined, let's register it so that the version purger uses it during the purge process.

We declare our custom advisor as a service and we tag it with the tag ``pim_versioning.purger.advisor``.

.. code-block:: yaml

    #Acme/Bundle/AppBundle/Resources/config/purger_advisors.yml
    parameters:
            acme_app.version_purger.skip_pair_version.class: Acme\Bundle\AppBundle\VersionPurger\SkipPairVersionPurgerAdvisor

    services:
        acme_app.version_purger.skip_pair_version:
            class: '%acme_app.version_purger.skip_pair_version.class%'
            tags:
                - { name: pim_versioning.purger.advisor, priority: 100 }


.. note::

    Do not forget to load the ``purger_advisors.yml`` configuration file in your bundle as well as clear the symfony cache.

Here we go! We can now run the purge command to observe that our advisor is correctly registered in the version purger.

You can add a few test versions in the PIM by modifying and saving a product or a family for instance.

.. code-block:: bash

    bin/console pim:versioning:purge --more-than-days 0 --env=prod

.. warning::

    It is important to run this command in production mode. ``--env=prod`` is mandatory in order to process high numbers of versions.

You can check in the PIM that the first, last and pair versions are kept in the history panel of any entity.
