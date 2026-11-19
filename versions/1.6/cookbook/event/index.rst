How to implement your business logic using the event system
===========================================================

The PIM uses the Symfony event system and fires events when something happens inside the system. For you, this means that
you can create your own `services`_ that interact with those objects whenever certain action happens within the PIM.

.. note::

    We highly recommend you to implement your business code on listeners, this way you will not depends on Akeneo or Pim
    interfaces, you will keep your code easily testable and you will not fear backward compatibility break anymore.

For more details about event listeners, you can read the `Symfony documentation`_

.. _Symfony documentation: http://symfony.com/doc/current/cookbook/event_dispatcher/event_listener.html
.. _services: http://symfony.com/doc/current/book/service_container.html

Example
-------

In the following example we will assume you want to be emailed about each product modification.
In order to do that you can create an event listener that will send the email.

.. code-block:: php

    # /src/Acme/Bundle/AppBundle/EventListener/ProductModificationListener.php
    <?php

    namespace Acme\Bundle\AppBundle\EventListener;

    use Symfony\Component\EventDispatcher\GenericEvent;
    use Pim\Bundle\CatalogBundle\Model\ProductInterface;

    class ProductModificationListener
    {
        private $mailer;

        public function __construct(\Swift_Mailer $mailer)
        {
            $this->mailer = $mailer;
        }

        public function onPostSave(GenericEvent $event)
        {
            $subject = $event->getSubject();

            if (!$subject instanceof ProductInterface) {
                // don't do anything if it's not a product
                return;
            }

            $message = \Swift_Message::newInstance()
                ->setSubject('A product modification event have been fired')
                ->setFrom('no-reply@example.com')
                ->setTo('me@example.com')
                ->setBody('...')
            ;

            $this->mailer->send($message);
        }
    }

And there is the service definition

.. configuration-block::

    .. code-block:: yaml

        # app/config/services.yml
        services:
            my.listener:
                class: Acme\Bundle\AppBundle\EventListener\ProductModificationListener
                arguments:
                    - '@swiftmailer.mailer'
                tags:
                    - { name: kernel.event_listener, event: akeneo.storage.post_save, method: onPostSave }

    .. code-block:: xml

        <!-- app/config/services.xml -->
        <service id="my.listener" class="Acme\Bundle\AppBundle\EventListener\ProductModificationListener">
            <tag name="kernel.event_listener" event="akeneo.storage.post_save" method="onPostSave" />
        </service>

    .. code-block:: php

        // app/config/services.php
        $container
            ->register('my.listener', 'Acme\Bundle\AppBundle\EventListener\ProductModificationListener')
            ->addTag('kernel.event_listener', array('event' => 'akeneo.storage.post_save', 'method' => 'onPostSave'))
        ;
