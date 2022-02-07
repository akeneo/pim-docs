How To Remove A Project On A Custom Catalog Update?
===================================================

Several catalog updates can have an impact on project integrity. In this case we need to remove the impacted projects.
Akeneo PIM already manages its own catalog updates.
The list is available in Teamwork Assistant :doc:`reference </technical_overview/teamwork_assistant/catalog_impact>` part.

How does It Work?
_________________

A subscriber handles catalog structure updates by listening to ``Akeneo\Tool\Component\StorageUtils\StorageEvents::POST_SAVE``
and ``Akeneo\Tool\Component\StorageUtils\StorageEvents::PRE_REMOVE`` on all entities and by dispatching them to the
``Akeneo\Pim\WorkOrganization\TeamWorkAssistant\Component\Remover\ChainedProjectRemover``. This chained project remover contains all
project removers and guesses which one has the responsibility to handle both the entity and the action it received.

A `ProjectRemover` has the responsibility to find and to remove all projects impacted by an entity and action pair.

How To Add A Custom Project Remover?
____________________________________

Reference data will be a good example to demonstrate how to add a `ProjectRemover`. We will take the example of the
Color reference data which is provided by default. See pim-community-dev repository ``app/config.yml`` and
``app/AppKernel.php`` to enable it.

Each project remover must implement the ``Akeneo\Pim\WorkOrganization\TeamWorkAssistant\Component\Remover\ProjectRemoverInterface``
interface and be tagged ``pimee_activity_manager.project_remover``.

.. code-block:: php

    <?php // src/AcmeEnterprise/Bundle/AppBundle/Remover/ColorProjectRemover.php

    namespace AcmeEnterprise\Bundle\AppBundle\Remover;

    use AcmeEnterprise\Bundle\AppBundle\Entity\Color;
    use Akeneo\Tool\Component\StorageUtils\Detacher\ObjectDetacherInterface;
    use Akeneo\Tool\Component\StorageUtils\Remover\RemoverInterface;
    use Akeneo\Tool\Component\StorageUtils\StorageEvents;
    use Doctrine\Common\Persistence\ObjectRepository;
    use Akeneo\Pim\WorkOrganization\TeamworkAssistant\Component\Model\ProjectInterface;
    use Akeneo\Pim\WorkOrganization\TeamworkAssistant\Component\Remover\ProjectRemoverInterface;

    class ColorProjectRemover implements ProjectRemoverInterface
    {
        /** @var RemoverInterface */
        protected $projectRemover;

        /** @var ObjectRepository */
        protected $projectRepository;

        /** @var ObjectDetacherInterface */
        protected $detacher;

        /**
         * @param ObjectRepository        $projectRepository
         * @param RemoverInterface        $projectRemover
         * @param ObjectDetacherInterface $detacher
         */
        public function __construct(
            ObjectRepository $projectRepository,
            RemoverInterface $projectRemover,
            ObjectDetacherInterface $detacher
        ) {
            $this->projectRepository = $projectRepository;
            $this->projectRemover = $projectRemover;
            $this->detacher = $detacher;
        }

        /**
         * This method is called by the ChainedProjectRemover to determine which ProjectRemover matches the
         * entity/action pair it receives.
         *
         * Does this project remover support the given entity/action?
         *
         * @param mixed  $entity (here renamed as $color)
         * @param string $action
         *
         * @return bool
         */
        public function isSupported($color, $action = null)
        {
            return $color instanceof Color && StorageEvents::PRE_REMOVE === $action;
        }

        /**
         * A project is removed if it uses a Color ReferenceData as product filter that is removed.
         *
         * Removes projects that have to be removed in terms of the given entity and action.
         *
         * @param mixed  $entity (here renamed as $color)
         * @param string $action
         */
        public function removeProjectsImpactedBy($color, $action = null)
        {
            /**
             * Don't be afraid to use `projectRepository->findAll()` as this method has been rewritten in
             * our repository to return a `Akeneo\Tool\Component\StorageUtils\Cursor\CursorInterface`. It does not hydrate
             * all projects at the same time. But don't forget to detach projects that are not removed to avoid
             * memory leaks.
             */
            foreach ($this->projectRepository->findAll() as $project) {
                if ($this->hasToBeRemoved($project, $color)) {
                    $this->projectRemover->remove($project);
                } else {
                    $this->detacher->detach($project);
                }
            }
        }

        /**
         * Determines if passed project has to be removed.
         *
         * @param ProjectInterface $project
         * @param Color            $color
         *
         * @return bool
         */
        protected function hasToBeRemoved(ProjectInterface $project, Color $color)
        {
            $colorCode = $color->getCode();
            $filters = $project->getProductFilters();
            foreach ($filters as $filter) {
                if (is_array($filter['value']) && in_array($colorCode, $filter['value'])) {
                    return true;
                }
            }

            return false;
        }
    }

Once your ``ProjectRemover`` is done you need to register it in the dependency injection and tag it.

.. code-block:: yaml

    # src/AcmeEnterprise/Bundle/AppBundle/Resources/config/services.yml
    services:
        acme_app.project_remover.color:
            class: 'AcmeEnterprise\Bundle\AppBundle\Remover\ColorProjectRemover'
            arguments:
                - '@pimee_activity_manager.repository.project'
                - '@pimee_activity_manager.remover.project'
                - '@akeneo_storage_utils.doctrine.object_detacher'
            public: false
            # All project removers MUST be tagged as follows to be managed by the ChainedProjectRemover
            tags:
                - { name: pimee_activity_manager.project_remover }

Now, when a user removes a Color that is used in projects as product filter, they are removed to avoid project integrity
alteration.
