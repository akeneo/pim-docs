Workflow events (Enterprise Edition only)
=========================================

Workflow events are dispatched in the PIM on some workflow actions, mainly about product draft steps (*sent for approval, rejected, deleted, approved...*).
They are basically useful if you want to add your business logic before or after those specific actions.


``pimee_workflow.product_draft.post_approve``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

This event is dispatched after a product draft proposal is approved.

These are the built-in PIM subscribers registered to this event:

================================  ===============
Listener Class Name               Priority
================================  ===============
`ApproveNotificationSubscriber`_  0
================================  ===============

.. _ApproveNotificationSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/ApproveNotificationSubscriber.php

``pimee_workflow.product_draft.post_ready``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

This event is dispatched after a product draft has been sent for review.

These are the built-in PIM subscribers registered to this event:

============================  ===============
Listener Class Name           Priority
============================  ===============
`SendForApprovalSubscriber`_  0
============================  ===============

.. _SendForApprovalSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/SendForApprovalSubscriber.php

``pimee_workflow.product_draft.post_refuse``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

This event is dispatched after a product draft proposal has been rejected.

These are the built-in PIM subscribers registered to this event:

===============================  ===============
Listener Class Name              Priority
===============================  ===============
`RefuseNotificationSubscriber`_  0
===============================  ===============

.. _RefuseNotificationSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/RefuseNotificationSubscriber.php

``pimee_workflow.product_draft.post_remove``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `PublishedProductEvent <https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/Event/PublishedProductEvent.php>`_

This event is dispatched after a product draft proposal is removed.

These are the built-in PIM subscribers registered to this event:

================================  ===============
Listener Class Name               Priority
================================  ===============
`RemoveNotificationSubscriber`_   0
================================  ===============

.. _RemoveNotificationSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/RemoveNotificationSubscriber.php


``pimee_workflow.published_product.post_publish``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Event Class: `PublishedProductEvent <https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/Event/PublishedProductEvent.php>`_

This event is dispatched after a product is published.

These are the built-in PIM subscribers registered to this event:

=====================================  ===============
Listener Class Name                    Priority
=====================================  ===============
`DetachProductPostPublishSubscriber`_  0
=====================================  ===============

.. _DetachProductPostPublishSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/PublishedProduct/DetachProductPostPublishSubscriber.php
