How to Customize the Import Behavior
====================================

Importations in the PIM are largely automated by using the Doctrine metadata, through a set of
guessers and property transformers. Import behavior is customizable for all entities by adding
new prioritized guessers and transformers.

Creating a Guesser
------------------

Guessers are used at the beginning of each importation to link each property of the imported data to
a property transformer.

For example, to skip all columns that begin with a ``#``, the following guesser could be created:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Transformer/Guesser/CommentGuesser.php
   :language: php
   :linenos:

The way the guesser works is simple:

* If the column's title does not start with a ``#``, ``null`` is returned.
* If the column starts with a ``#``, an array containing the transformer service and its options is returned

The guesser must be included in the DIC this way:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/guessers.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/DemoConnectorBundle/Resources/config/guessers.yml
   :linenos:

As you can see, the transformer returned by the guesser will be the ``pim_import_export.transformer.property.skip``
transformer.

Creating a Transformer
----------------------

In the following example, we are going to create a transformer which prepends a string to a scalar value:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Transformer/Property/PrependTransformer.php
   :language: php
   :linenos:

This transformer requires that the string to prepend is passed in the options, you should therefore add
a custom guesser which will do so.

To add the transformer to the DIC, proceed the following way:

.. literalinclude:: ../../src/Acme/Bundle/DemoConnectorBundle/Resources/config/transformers.yml
   :language: yaml
   :prepend: # /src/Acme/Bundle/DemoConnectorBundle/Resources/config/transformers.yml
   :linenos:
