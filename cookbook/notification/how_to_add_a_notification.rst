How to Add a Notification
=========================

The Akeneo PIM comes with a notification system.

Prerequisites
-------------

If you need to send a custom message for an existing notification type (like import/export, mass edit, etc.)
you can go to the `Inject Services`_ step.
If you want to create your own notification type for a custom bundle, you will need to create your notification factory.

This cookbook assumes that you already created a new bundle to add your new Notification. Let's assume its namespace is
`Acme\\CustomBundle`.

Create a Notification Factory
-----------------------------

.. code-block:: php

    # /src/Acme/Bundle/CustomBundle/Notification/CustomNotificationFactory.php
    <?php

    namespace Acme\CustomBundle\Notification;

    use Pim\Bundle\NotificationBundle\Entity\Notification
    use Pim\Bundle\NotificationBundle\Factory\NotificationFactoryInterface;

    class CustomNotificationFactory implements NotificationFactoryInterface
    {
        /** @var array */
        protected $notificationTypes;

        /**
         * @param array $notificationTypes
         */
        public function __construct(array $notificationTypes)
        {
            $this->notificationTypes = $notificationTypes;
        }

        /**
         * {@inheritdoc}
         */
        public function create($status, $message, $messageParams, $route, $routeParams, $type)
        {
            // Here is a simple implementation of the create method, you can add your custom code.
            $notification = new Notification();

            $notification
                ->setType($status)                 // The notification type ('success', 'warning' or 'error')
                ->setMessage($message)             // The message translation key
                ->setMessageParams($messageParams) // Parameters for the message
                ->setRoute($route)
                ->setRouteParams($routeParams)
                ->setContext(['actionType' => $type]);

            return $notification;
        }

        /**
         * {@inheritdoc}
         */
        public function supports($type)
        {
            return in_array($type, $this->notificationTypes);
        }
    }

Adding a route will redirect users to this route when the notification is clicked.

For example, the route of the show export profile page is:

.. code-block:: yaml

    pim_importexport_export_profile_show:
        path: /{id}
        defaults: { _controller: pim_import_export.controller.export_profile:showAction }
        requirements:
            id: \d+

The optional route parameter will be:

.. code-block:: php

    $route = 'pim_importexport_export_execution_show';
    $routeParams = ['id' => $jobExecutionId];

If the messageParams option is provided, it will be passed to the message when translating it.

The context allows to store some extra data in the notification, it is not displayed in the UI by default.

Now register your new factory with the proper tag:

.. code-block:: yaml

    services:
        acme_custom.notification.factory.custom_notification_factory:
            class: 'Acme\CustomBundle\Notification\CustomNotificationFactory'
            arguments:
                - ['my_custom_name']
            tags:
                - { name: pim_notification.factory.notification }

With this tag you will be able to get your new factory from the dedicated registry.

Inject Services
---------------

The notifier service is called: ``@pim_notification.notifier`` and the factory registry is
``@pim_notification.registry.factory.notification``.

.. code-block:: php

    services:
        acme_custom.event_subscriber.custom_subscriber:
            class: 'Acme\CustomBundle\EventSubscriber\CustomEventSubscriber'
            arguments:
                - '@pim_notification.registry.factory.notification'
                - '@pim_notification.notifier'
            tags:
                - { name: kernel.event_subscriber }

Here, we inject services in an event subscriber, but you can inject them wherever you have an action which notifies a user.

Then, add it to your constructor as follows:

.. code-block:: php

    # /src/Acme/Bundle/CustomBundle/EventSubscriber/CustomEventSubscriber.php

    /** @var NotificationFactoryRegistry */
    protected $factoryRegistry;

    /** @var Notifier */
    protected $notifier;

    /**
     * @param NotificationFactoryRegistry $factoryRegistry
     * @param Notifier                    $notifier
     */
    public function __construct(NotificationFactoryRegistry $factoryRegistry, Notifier $notifier)
    {
        $this->factoryRegistry = $factoryRegistry;
        $this->notifier        = $notifier;
    }

Notify Users
------------

.. code-block:: php

    $factory = $this->factoryRegistry->get('my_custom_name');
    $notification = $factory->create($status, $message, $messageParams, $route, $routeParams, $type);

    $this->notifier->notify(
        $notification,
        [$user1, $user2] // An array of users (UserInterface or just the username)
    );
