How to use Presenters
=====================

The Presenters are used to display the data for the end user according to their locale.
For example, a metric value will be displayed `"0.15 Meter"` in English and `"0,15 Mètre"` in French.
In this page you will learn how to create your own Presenters or modify predefined formats of default Presenters.

How to register a new Presenter
-------------------------------

Several presenters are already defined for prices, metrics, numbers, etc.
You can implement your own Presenter to display your custom values.

To create your new Presenter, first create a class implementing `Akeneo\\Component\\Localization\\Presenter\\PresenterInterface`.
Next, declare it as a service:

.. code-block:: yaml

    pim_catalog.localization.presenter.my_own_presenter:
        class: %my_own_presenter_class%
        tags:
            - { name: pim_catalog.localization.presenter, type: 'product_value' }

You have to tag your service with one of the following types:

- `product_value`: used to present the `ProductValue` (e.g. prices, metrics, numbers, etc),
- `attribute_option`: used to present the attribute options (e.g. the option `max_number` of the number attributes).

Actually, the Presenter interface displays data according to the user locale, but you can create a Presenter using custom options.


How to modify the predefined formats of default Presenters
----------------------------------------------------------

.. _IntlDateFormatter: http://php.net/manual/en/class.intldateformatter.php
.. _NumberFormatter: http://php.net/manual/en/class.numberformatter.php

You can define your own formats to present dates, datetimes and prices depending on the user's locale.
The formats of date, datetimes and currencies are predefined for a set of languages.
When the requested format is not predefined, the fallback is defined by `IntlDateFormatter`_ and `NumberFormatter`_.

To add or modify the default formats, you have to add or modify the `pim_catalog.localization.factory` tags (`date.formats`, `datetime.formats` and `currency.formats`).

For example:

.. code-block:: yaml

    pim_catalog.localization.factory.currency.formats:
        en_US: '¤#,##0.00'
        fr_FR: '#,##0.00 ¤'
