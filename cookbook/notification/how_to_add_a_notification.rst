How to Add a Notification
=========================

The Akeneo PIM comes with a notification system.

In a simple way, you can add your notifications to the notifier icon on any action.

Prerequisites
-------------

The Akeneo PIM project introduces services to help you manage your notifications.

* Inject the manager service

The service is called : @pim_notification.manager.user_notification

.. code-block:: php

    services:
        pim_notification.event_subscriber.job_execution_notifier:
            class: %pim_notification.event_subscriber.job_execution_notifier.class%
            arguments:
                - '@pim_notification.manager.user_notification'
            tags:
                - { name: kernel.event_subscriber }

Here, we inject the service in an event subscriber but you can inject it wherever
you have an action to notify to a user.

Then, add it in your constructor

.. code-block:: php

    /** @var UserNotificationManager */
    protected $manager;

    /**
     * @param UserNotificationManager $manager
     */
    public function __construct(UserNotificationManager $manager)
    {
        $this->manager = $manager;
    }

Notify Users
------------

* Notify users

.. code-block:: php

    $this->manager->notify(
        [$user1, $user2],
        'Your awesome message to users',
        'success',
        $options
    );

Three states are compatibles with our icons : 'success', 'warning' and 'error'.

You can add options to your notification, by default options are :

.. code-block:: php

    [
        'route' => '',
        'routeParams' => [],
        'messageParams' => [],
        'context => ''
    ]

Adding a route allows users to navigate somewhere you want by clicking on the notification.

For example, routing of the show export profile is :

.. code-block:: php

    pim_importexport_export_profile_show:
        path: /{id}
        defaults: { _controller: pim_import_export.controller.export_profile:showAction }
        requirements:
            id: \d+

The optional route parameter will be :

.. code-block:: php

    $options = [
        'route' => 'pim_importexport_export_execution_show',
        'routeParams' => [
            'id' => $jobExecutionId
        ]
    ];
