How to add a custom unit of measure
===================================

If Akeneo PIM didn´t provide all the measures you need, you can easily add a custom unit of measure on an existing family.

The custom measures are configured by a ``Resources/config/measure.yml`` file.
This Yaml file could be in every custom bundle registered by the AppKernel.php and will be processed for the Akeneo MeasureBundle.

In this example we will add the MICROMETER unit to the length measure family in a own MyMeasure Bundle.

Create our Bundle
-----------------

Create a new Symfony bundle:

.. code-block:: php
	
	<?php

	namespace Acme\Bundle\MyMeasureBundle;

	use Symfony\Component\HttpKernel\Bundle\Bundle;

	class AcmeMyMeasureBundle extends Bundle
	{
	}
	
Register the bundle in AppKernel:

.. code-block:: php

    public function registerProjectBundles()
    {
        // ...
            new Acme\Bundle\MyMeasureBundle\AcmeMyMeasureBundle(),
        // ...
    }

Configure our unit of measure
-----------------------------
	
Create a file ``Resources/config/measure.yml`` in our Bundle to configure the unit of measure:

.. code-block:: yaml
	
    measures_config:
    Length:
        standard: METER
        units:
            MICROMETER:
                convert: [{'mul': 0.000001}]
                symbol: μm

Here, we just add the "MICROMETER" unit with his conversion rules from it to standard unit. To have equivalent to 1 micrometer in meters, you must multiply by 0,000001. A symbol is required too to define unit format to display. 

.. tip::
	For more examples you can find the original measure.yml here https://github.com/akeneo/MeasureBundle/blob/master/Resources/config/measure.yml

Translate our custom unit
-------------------------

Create the files ``Resources/translations/messages.en.yml`` and ``Resources/translations/jsmessages.en.yml`` in our Bundle to translate our custom unit of measure.

These two files contains this translation:

.. code-block:: yaml

	MICROMETER: micrometer
	
Finaly clear cache and try it out
---------------------------------

Delete all translation files inside of ``/web/js/translations/``.

Clear app cache with this command:

.. code-block:: bash

	rm -rf app/cache /*


Try to create a new attribute in Akeneo frontend with metric as the attribute type.

Choose "length" as the metric family.

Inside of the unit dropdown you should find your custom unit - in our case the "micrometer" unit.
