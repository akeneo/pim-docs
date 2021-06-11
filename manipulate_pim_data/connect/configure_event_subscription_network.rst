How to Configure the Event Subscription network restrictions
============================================================

In the Event Subscription, some restrictions exists on the entered URL to guarantee that the PIM is not used for accessing
unsolicited resources.
However, **you may need to change those rules**, this chapter explains how.

Add IPs to the whitelist
------------------------
If you want to allow specific IPs, you can set the environment variable `ALLOWED_NETWORK_WHITELIST` with
a comma-sperated list of IPs (with or without netmask).

.. code-block:: yaml
    :linenos:

    ALLOWED_NETWORK_WHITELIST=10.0.2.0/24,10.0.3.1
