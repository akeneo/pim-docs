Using Spin In Behat
===================

As our Behat tests are pretty slow, we had issues waiting for some Element to appear in pages. Sometimes an Element
is not found because the page isn't completely loaded. In this case, waiting two or three extra seconds could help
finding elements.

.. note::

    Learn about spin from `Behat documentation <http://docs.behat.org/en/v2.5/cookbook/using_spin_functions.html>`_.

Use the SpinCapableTrait
------------------------

In Akeneo PIM we use spin as a Trait. To be able to use the spin method you only have to add the Trait in your decorator
or your context as follows:

.. code-block:: php

    use Context\Spin\SpinCapableTrait;
    use Pim\Behat\Decorator\ElementDecorator;

    MyClassDecorator extends ElementDecorator {
        use SpinCapableTrait;
    }

Spin Signature
--------------

In the method signature you can see two arguments.

.. code-block:: php

    // Context\Spin\SpinCapableTrait

    public function spin($callable, $message) {}

The first one is a callable function. This function is where you search for your Element. If this callable returns
false, null or throws an exception, the spin will retry every second the callable function until it returns a value or
when the loop limit is reached.

The second one is the message that will be written in the console in case the loop limit is reached.

Example
-------

Let's see an example from the PermissionDecorator:

.. code-block:: php

    /**
     * @param string $group
     *
     * @return NodeElement
     */
    public function findGroup($group)
    {
        return $this->spin(function () use ($group) {
            return $this->find('css', sprintf('.tab-groups .tab:contains("%s")', $group));
        }, sprintf('Group "%s" not found.', $group));
    }

Here we want to find an Element which contains the text *$group*. Searching for this Element in the page without
spinning may not work as the loading of the page might not be complete. Waiting for the page to be completely loaded can
lead to a significant time waiting, all the while knowing we can never be certain when this event will actually occur.
Thanks to the spin, we just have to wait for a specific Element to appear. That's why we never use the wait method from
Behat.

.. note::

    Usually, we avoid adding actions on elements like *click()*, *check()*, etc. in spin, but sometimes it is
    unavoidable to perform a series of find with actions to deploy a panel or click on a button to reveal a field.

Inform That Spin Failed
-----------------------

There are two ways to inform the developer the callable function has failed. The first one is to fill the second
argument of the spin *$message* which was previously mentioned. The second one is to throw a SpinException from the
callable. If an exception is thrown from the spin, the result is that the spin will retry and the *$message* will be
replaced by the SpinException message.

.. note::

    Make sure to send a clear message with as much indications as you can provide to the developer about the location
    and the reason why the callable has failed.
