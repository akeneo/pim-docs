How to Configure the Event Subscription network restrictions
============================================================

In the Event Subscription, some restrictions exist on the entered URL to guarantee that the PIM is not used to access
unsolicited resources.

The following domains are always blacklisted:

- `localhost`
- `elasticsearch`
- `memcached`
- `object-storage`
- `mysql`

Additionally, we also block the ranges of IPs defined in the RFC1918.

However, **you may need to add an exception to those IP address ranges**, this chapter explains how.

Add IPs to the whitelist
------------------------
If you want to allow specific IPs, you can set the environment variable `ALLOWED_NETWORK_WHITELIST` with
a list of IPs separated by commas, with or without netmask.

.. code-block:: yaml
    :linenos:

    ALLOWED_NETWORK_WHITELIST=10.0.2.0/24,10.0.3.1

.. warning::

    The domain blacklist will always supersede the IP whitelist.
