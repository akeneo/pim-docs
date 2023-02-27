How to implement your business logic using the event system
===========================================================

The PIM uses the Symfony event system and fires events when something happens inside the system. For you, this means that
you can create your own `services`_ that interact with those objects whenever certain action happens within the PIM.

.. note::

    We highly recommend you to implement your business code on listeners, this way you will not depend on Akeneo or Pim
    interfaces, you will keep your code easily testable and you will not fear backward compatibility break anymore.

For more details about event listeners, you can read the `Symfony documentation`_

.. _Symfony documentation: https://symfony.com/doc/5.4/event_dispatcher.html
.. _services: https://symfony.com/doc/5.4/service_container.html

Example
-------

In the following example we will assume you want to be emailed about each product modification.
In order to do that you can create an event listener that will send the email.

.. code-block:: php

    # /src/Acme/Bundle/AppBundle/EventListener/ProductModificationListener.php
    <?php

    namespace Acme\Bundle\AppBundle\EventListener;

    use Symfony\Component\EventDispatcher\GenericEvent;
    use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Email;

    class ProductModificationListener
    {
        public function __construct(private MailerInterface $mailer)
        {
        }

        public function onPostSave(GenericEvent $event)
        {
            $subject = $event->getSubject();

            if (!$subject instanceof ProductInterface) {
                // don't do anything if it's not a product
                return;
            }

            $email = (new Email())
              ->subject('A product modification event have been fired')
              ->text('...')
              ->addTo('me@example.com')
              ->html('...');

            $this->mailer->send($email);
        }
    }

And there is the service definition:

.. code-block:: yaml

    # app/config/services.yml
    services:
        my.listener:
            class: Acme\Bundle\AppBundle\EventListener\ProductModificationListener
            arguments:
                - '@mailer'
            tags:
                - { name: kernel.event_listener, event: akeneo.storage.post_save, method: onPostSave }
