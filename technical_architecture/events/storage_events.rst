Storage events
==============

.. _GenericEvent: https://github.com/symfony/symfony/blob/4.4/src/Symfony/Component/EventDispatcher/GenericEvent.php
.. _RemoveEvent: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Tool/Component/StorageUtils/Event/RemoveEvent.php

Storage events are dispatched in the PIM when you manipulate data using the savers and removers services.
They are basically useful if you want to add your business logic before or after an object is saved or removed.

Here is a list of all the events:

``akeneo.storage.pre_remove``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `RemoveEvent`_

This event is dispatched before we remove an object using a remover.

These are the built-in PIM subscribers registered to this event:

================================================================  ===============
Listener Class Name                                               Priority
================================================================  ===============
`ChannelLocaleSubscriber`_                                        0
`CheckChannelsOnDeletionSubscriber`_                              0
`AddRemoveVersionSubscriber`_                                     0
RuleRelationSubscriber (Enterprise edition)                       0
RemoveChannelSubscriber (Enterprise edition)                      0
CheckPublishedProductOnRemovalSubscriber (Enterprise edition)     0
AssetEventSubscriber (Enterprise edition)                         0
CatalogUpdatesSubscriber (Enterprise edition)                     0
ProjectSubscriber (Enterprise edition)                            0
CheckPublishedProductOnRemovalSubscriber (Enterprise edition)     0
================================================================  ===============

.. _CheckChannelsOnDeletionSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Pim/Enrichment/Bundle/EventSubscriber/Category/OnDelete/CheckChannelsOnDeletionSubscriber.php
.. _AddRemoveVersionSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Tool/Bundle/VersioningBundle/EventSubscriber/AddRemoveVersionSubscriber.php

``akeneo.storage.post_remove``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `RemoveEvent`_

This event is dispatched after we have removed an object using a remover.

These are the built-in PIM subscribers registered to this event:

===============================================================  ===============
Listener Class Name                                              Priority
===============================================================  ===============
`Product\\OnDelete\\ComputeProductsAndAncestorsSubscriber`_      0
`ProductModel\\OnDelete\\ComputeProductAndAncestorsSubscriber`_  0
===============================================================  ===============

.. _Product\\OnDelete\\ComputeProductsAndAncestorsSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Pim/Enrichment/Bundle/EventSubscriber/Product/OnDelete/ComputeProductsAndAncestorsSubscriber.php
.. _ProductModel\\OnDelete\\ComputeProductAndAncestorsSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Pim/Enrichment/Bundle/EventSubscriber/ProductModel/OnDelete/ComputeProductAndAncestorsSubscriber.php

``akeneo.storage.pre_remove_all``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `RemoveEvent`_

This event is dispatched before we remove a pool of objects using the removeAll function of the saver.

``akeneo.storage.post_remove_all``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `RemoveEvent`_

This event is dispatched after we have removed  a pool of objects using the removeAll function of the saver.

``akeneo.storage.pre_save``
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent`_

This event is dispatched before we save an object using a saver, either when saving one object or a pool of objects, using save and saveAll methods of the saver.

These are the built-in PIM subscribers registered to this event:

=============================================================  ===============
Listener Class Name                                            Priority
=============================================================  ===============
`CurrencyDisablingSubscriber`_                                 0
`ComputeEntityRawValuesSubscriber`_                            0
`ChannelLocaleSubscriber`_                                     0
MergeNotGrantedProductDataSubscriber (Enterprise edition)      0
ProjectSubscriber (Enterprise edition)                         0
EnsureProductDraftGlobalStatusSubscriber (Enterprise edition)  0
=============================================================  ===============

.. _CurrencyDisablingSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Channel/back/Infrastructure/EventListener/CurrencyDisablingSubscriber.php
.. _ComputeEntityRawValuesSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Pim/Enrichment/Bundle/EventSubscriber/EntityWithValues/ComputeEntityRawValuesSubscriber.php
.. _ChannelLocaleSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Channel/back/Infrastructure/EventListener/ChannelLocaleSubscriber.php

``akeneo.storage.post_save``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent`_

This event is dispatched after we have saved an object using a saver, either when saving one object or a pool of objects, using save and saveAll methods of the saver.

These are the built-in PIM subscribers registered to this event:

===============================================================  ===============
Listener Class Name                                              Priority
===============================================================  ===============
`Product\\OnSave\\ComputeProductsAndAncestorsSubscriber`_        0
`ProductModel\\OnSave\\ComputeProductAndAncestorsSubscriber`_    0
CatalogUpdatesSubscriber (Enterprise edition)                    0
ImportProposalsSubscriber (Enterprise edition)                   0
===============================================================  ===============

.. _Product\\OnSave\\ComputeProductsAndAncestorsSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Pim/Enrichment/Bundle/EventSubscriber/Product/OnSave/ComputeProductsAndAncestorsSubscriber.php
.. _ProductModel\\OnSave\\ComputeProductAndAncestorsSubscriber: https://github.com/akeneo/pim-community-dev/blob/master/src/Akeneo/Pim/Enrichment/Bundle/EventSubscriber/ProductModel/OnSave/ComputeProductAndAncestorsSubscriber.php


``akeneo.storage.pre_save_all``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent`_

This event is dispatched before we save a pool of objects using the saveAll function of the saver.

These are the built-in PIM subscribers registered to this event:

===========================================================  ===============
Listener Class Name                                          Priority
===========================================================  ===============
AddAttributeGroupPermissionsSubscriber (Enterprise edition)  0
AddCategoryPermissionsSubscriber (Enterprise edition)        0
===========================================================  ===============

``akeneo.storage.post_save_all``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent`_

This event is dispatched after we have saved a pool of objects using the saveAll function of the saver.

These are the built-in PIM subscribers registered to this event:

=============================================================  ===============
Listener Class Name                                            Priority
=============================================================  ===============
`Product\\OnSave\\ComputeProductsAndAncestorsSubscriber`_      0
`ProductModel\\OnSave\\ComputeProductAndAncestorsSubscriber`_  0
AddAttributeGroupPermissionsSubscriber (Enterprise edition)    0
AddCategoryPermissionsSubscriber (Enterprise edition)          0
=============================================================  ===============
