Customize notifications
=======================

Overview
--------

Currently users are notified through the notification center, but you can easily customize this system to add your own notifications.
Let's explain how to send an email to the users for instance. At the end of the project creation, an event is dispatched: ``PimEnterprise\Component\TeamworkAssistant\Event\ProjectEvents::PROJECT_CALCULATED``.
You need to subscribe to this event to send an email.

.. note::

    The workflow of the project creation is explained `here <project_creation>`

Add a new event subscriber
--------------------------

First you need to create a new subscriber in your bundle, example: ``src\AppBundle\EventListener``.

.. code-block:: php

    namespace AppBundle\EventListener;

    use Akeneo\Bundle\BatchBundle\Notification\MailNotifier;
    use PimEnterprise\Component\TeamworkAssistant\Repository\UserRepositoryInterface;

    class ProjectCreationNotifierSubscriber implements EventSubscriberInterface
    {
        /** @var MailNotifier */
        private $notifier;

        /** @var UserRepositoryInterface */
        private $userRepository;

        /**
         * @param MailNotifier            $notifier
         * @param UserRepositoryInterface $userRepository
         */
        public function __construct(
            MailNotifier $notifier,
            UserRepositoryInterface $userRepository
        ) {
            $this->notifier = $notifier;
            $this->userRepository = $userRepository;
        }

        /**
         * {@inheritdoc}
         */
        public static function getSubscribedEvents()
        {
            return [
                ProjectEvents::PROJECT_CALCULATED => 'projectCreated',
            ];
        }

        /**
         * Notifies users that belong to user groups linked to the project without the project owner.
         *
         * @param ProjectEvent $event
         */
        public function projectCreated(ProjectEvent $event)
        {
            $project = $event->getProject();

            if (empty($project->getUserGroups())) {
                return;
            }

            $userGroups = $project->getUserGroups();
            $owner = $project->getOwner();

            $userGroupIds = [];
            foreach ($userGroups as $userGroup) {
                $userGroupIds[] = $userGroup->getId();
            }

            $users = $this->userRepository->findContributorsToNotify($owner->getId(), $userGroupIds);

            $this->notifier->notify($users, 'subject', 'You have new products to enrich');
        }
    }

And don't forget to add the service definition, example: ``src\AppBundle\Resources\config\services.yml``:

.. code-block:: yaml

    project_creation_notifier:
        class: 'AppBundle\EventListener\ProjectCreationNotifierSubscriber'
        arguments:
            - '@pim_notification.email.email_notifier'
            - '@activity_manager.repository.doctrine.project'
            - '@activity_manager.repository.user'
        tags:
            - { name: kernel.event_subscriber }
