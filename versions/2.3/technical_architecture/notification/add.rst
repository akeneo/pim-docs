How to Add a Notification
=========================

The Akeneo PIM comes with a notification system.

Prerequisites
-------------

If you need to send a custom message for an existing notification type (like import/export, mass edit, etc.)
you can go to the `Inject Services`_ step.

If you want to create **your own notification type for a custom bundle**, you will need to create your notification factory.

.. note::
    This cookbook assumes that you already created a new bundle to add your new Notification. Let's assume its namespace is ``Acme\CustomBundle``.

Create a Notification Factory
-----------------------------

First we'll need to create the Notification Factory that will build our own notifications. This class should implement ``Pim\Bundle\NotificationBundle\Factory\NotificationFactoryInterface``.

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
        public function create($object)
        {
            $notification = new Notification(); // we'll setup it just after

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

Let's take a look at what's important here, the ``create`` method of this factory:

.. code-block:: php

    public function create($object)
    {
        $notification = new Notification();

        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setMessageParams($messageParams);
        $notification->setRoute($route);
        $notification->setRouteParams($routeParams);
        $notification->setContext($context);

        return $notification;
    }

Note that ``$object`` is ``mixed``, so feel free to give anything useful to build your notification. For instance, in our internal job notifications, we directly send the JobExecution to the ``create`` method of our Notification Factories.


1) Type (``string``)
    Type of the notification. Can be ``success``, ``warning`` or ``error``.
2) Message (``string``)
    The message to display in the notification. It can be a simple string, or a translation key, eg. ``pim_import_export.notification.export.success``
3) MessageParams (``array``)
    The message parameters to give to the translation key, if any. Eg. ``['%label%' => 'Product export']``
    If provided, it will be passed to the message when translating it.
4) Route (``string``)
    The route the user will be redirected to if the notification is clicked.
5) RouteParams (``array``)
    The parameters to that given route.
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

6) Context (``array``)
    The context allows to store some extra data in the notification, it is not displayed in the UI by default. Some important extra data you may use are:
        - ``actionType`` (``string``): this will be used to guess the icon to display on the notification
        - ``showReportButton`` (``bool``): to hide/display the "report" label on the notification

.. note::
    You can see available notification icons on the `styleguide website <https://docs.akeneo.com/2.3/design_pim/styleguide/index.php#Templates-AknNotification>`_.

For example, the ``create`` method of the NotificationFactory for mass edit notifications looks like that:

.. code-block:: php

    public function create($jobExecution)
    {
        $notification = new Notification();
        $type = $jobExecution->getJobInstance()->getType();
        $status = $this->getJobStatus($jobExecution);

        $notification
            ->setType($status)
            ->setMessage(sprintf('pim_mass_edit.notification.%s.%s', $type, $status))
            ->setMessageParams(['%label%' => $jobExecution->getJobInstance()->getLabel()])
            ->setRoute('pim_enrich_job_tracker_show')
            ->setRouteParams(['id' => $jobExecution->getId()])
            ->setContext(['actionType' => $type]);

        return $notification;
    }

Well, now we created our very own Notification Factory, **we need to register it** with the proper tag:

.. code-block:: yaml

    services:
        acme_custom.notification.factory.custom_notification_factory:
            class: 'Acme\CustomBundle\Notification\CustomNotificationFactory'
            arguments:
                - ['my_custom_notification_name']
            tags:
                - { name: pim_notification.factory.notification }

With this tag we will be able to get our new factory from the dedicated registry.

Inject Services
---------------

Now that our Notification Factory is created and registered, we can build our own notifications!

.. warning::
    The Notification Factory we just created is **not responsible for sending notifications**, only to build them.
    To send notification, we need to **call the Notifier**.

To send our notifications, we'll need to:
    1) Retrieve our factory with the notification factory registry (``@pim_notification.registry.factory.notification``)
    2) Build the notification
    3) Give it to the Notifier (``@pim_notification.notifier``) to actually notify users

So we'll need 2 services:

.. code-block:: php

    services:
        acme_custom.event_subscriber.custom_subscriber:
            class: 'Acme\CustomBundle\EventSubscriber\CustomEventSubscriber'
            arguments:
                - '@pim_notification.registry.factory.notification'
                - '@pim_notification.notifier'
            tags:
                - { name: kernel.event_subscriber }

Here, we inject services in an event subscriber, but we can inject them wherever we have an action which notifies a user.
Then, let's add it to our constructor as follows:

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

Now everything is plugged together, let's send some notifications!

.. code-block:: php

    // 1) retrieve our factory with the notification factory registry
    $factory = $this->factoryRegistry->get('my_custom_notification_name');

    // 2) build the notification
    $notification = $factory->create($status, $message, $messageParams, $route, $routeParams, $type);

    // 3) give it to the Notifier to actually notify users
    $this->notifier->notify(
        $notification,
        [$user1, $user2] // An array of users (UserInterface or just the username)
    );
