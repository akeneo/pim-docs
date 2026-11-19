How to Add a Notification
=========================

The Akeneo PIM comes with a notification system.

In a simple way, you can add your notifications to the notifier widget.

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your notifications.

* Inject the manager service

The service is called: ``@pim_notification.manager.notification``

.. code-block:: yaml

    services:
        pim_notification.event_subscriber.job_execution_notifier:
            class: '%pim_notification.event_subscriber.job_execution_notifier.class%'
            arguments:
                - '@pim_notification.manager.notification'
            tags:
                - { name: kernel.event_subscriber }

Here, we inject the service in an event subscriber, but you can inject it wherever
you have an action to notify a user.

Then, add it to your constructor as follows:

.. code-block:: php

    /** @var NotificationManager */
    protected $manager;

    /**
     * @param NotificationManager $manager
     */
    public function __construct(NotificationManager $manager)
    {
        $this->manager = $manager;
    }

Notify Users
------------

* Notify users

.. code-block:: php

    $this->manager->notify(
        [$user1, $user2],       // An array of users (UserInterface or just the username)
        'Your awesome message', // The message translation key
        'success',              // The notification type ('success', 'warning' or 'error')
        $options                // Additional options
    );

The default options are:

.. code-block:: php

    [
        'route' => '',
        'routeParams' => [],
        'messageParams' => [],
        'context => ''
    ]

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

    $options = [
        'route' => 'pim_importexport_export_execution_show',
        'routeParams' => [
            'id' => $jobExecutionId
        ]
    ];

If the messageParams option is provided, it will be passed to the message when translating it.

The context allows to store some extra data in the notification, it is not displayed in the UI by default.
