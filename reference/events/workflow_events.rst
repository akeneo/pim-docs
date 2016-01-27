Workflow events (Enterprise Edition only)
=========================================

Workflow events are dispatched in the PIM on some workflow actions, mainly about product draft steps (*sent for approval, rejected, deleted, approved...*) and publish feature.
They are basically useful if you want to add your business logic before or after those specific actions.

Publish Workflow Event Actions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When a product is published:
    1) ``pimee_workflow.published_product.pre_publish``
    2) The product object is published
    3) ``pimee_workflow.published_product.post_publish`` (**only on flush!**)

When a product is unpublished:
    1) ``pimee_workflow.published_product.pre_unpublish``
    2) The product object is unpublished
    3) ``pimee_workflow.published_product.post_unpublish`` (**only on flush!**)

``pimee_workflow.published_product.pre_publish``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product has been published.

**Event Class**: `PublishedProductEvent <https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/Event/PublishedProductEvent.php>`_

``pimee_workflow.published_product.post_publish``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **after** a product has been published.

**Event Class**: `PublishedProductEvent <https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/Event/PublishedProductEvent.php>`_

**Built-in PIM subscribers registered to this event**

=====================================  ===============
Listener Class Name                    Priority
=====================================  ===============
`DetachProductPostPublishSubscriber`_  0
=====================================  ===============

``pimee_workflow.published_product.pre_unpublish``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product has been unpublished.

**Event Class**: `PublishedProductEvent <https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/Event/PublishedProductEvent.php>`_

``pimee_workflow.published_product.post_unpublish``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **after** a product has been unpublished.

**Event Class**: `PublishedProductEvent <https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/Event/PublishedProductEvent.php>`_

Draft/Proposal Workflow Event Actions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When a product draft is sent for approval:
    1) ``pimee_workflow.product_draft.pre_ready``
    2) The product draft **status is updated** to be reviewed
    3) ``pimee_workflow.product_draft.post_ready``

When a product draft is (partially) approved:
    1) ``pimee_workflow.product_draft.pre_approve`` (or ``pimee_workflow.product_draft.pre_partial_approve``)
    2) The product is **fetched from the product draft**
    3) ``pimee_workflow.product_draft.pre_apply``
    4) The **product object is updated** with product draft values (**but not saved yet**)
    5) ``pimee_workflow.product_draft.post_apply``
    6) The **product is saved** with the new values, and the **draft is updated/removed**
    7) ``pimee_workflow.product_draft.post_approve`` (or ``pimee_workflow.product_draft.post_partial_approve``)

When a product draft is refused:
    1) ``pimee_workflow.product_draft.pre_refuse``
    2) The product **draft status is updated**
    3) ``pimee_workflow.product_draft.post_refuse``

When a product draft is deleted:
    1) ``pimee_workflow.product_draft.pre_remove``
    2) Product draft is **removed from the DB**
    3) ``pimee_workflow.product_draft.post_remove``

``pimee_workflow.product_draft.pre_ready``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft status has been set to "ready".

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.post_ready``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **after** a product draft status has been set to "ready" and saved to DB.
The product draft now becomes a proposal, **ready to be reviewed**.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

**Built-in PIM subscribers registered to this event**

============================  ===============
Listener Class Name           Priority
============================  ===============
`SendForApprovalSubscriber`_  0
============================  ===============

``pimee_workflow.product_draft.pre_approve``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft has been approved.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.post_approve``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **after** a product draft has been approved.
The product **has been updated and saved** with the new values, and the product draft has been removed or updated.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

**Built-in PIM subscribers registered to this event**

================================  ===============
Listener Class Name               Priority
================================  ===============
`ApproveNotificationSubscriber`_  0
================================  ===============

``pimee_workflow.product_draft.pre_partial_approve``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft has been **partially** approved.
A partial approve could be about a specific attributes/locale/channel of the product draft.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.post_partial_approve``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft has been **partially** approved.
The product **has been updated and saved** with the new values, and the product draft has been removed or updated.
A partial approve could be about a specific attributes/locale/channel of the product draft.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

**Built-in PIM subscribers registered to this event**

================================  ===============
Listener Class Name               Priority
================================  ===============
`ApproveNotificationSubscriber`_  0
================================  ===============

``pimee_workflow.product_draft.pre_apply``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product object has been updated from draft values.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.post_apply``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **after** a product object has been updated from draft values.
Note that **the product has not been saved yet**, only the product object has been updated.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.pre_refuse``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft has been refused.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.post_refuse``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **after** a product draft has been refused.
The product draft has been updated in the DB.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

**Built-in PIM subscribers registered to this event**

===============================  ===============
Listener Class Name              Priority
===============================  ===============
`RefuseNotificationSubscriber`_  0
===============================  ===============

``pimee_workflow.product_draft.pre_partial_refuse``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft has been **partially** refused.
A partial refuse could be about a specific attributes/locale/channel of the product draft.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.post_partial_refuse``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft has been **partially** refused.
The product draft has been removed or updated.
A partial refuse could be about a specific attributes/locale/channel of the product draft.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

**Built-in PIM subscribers registered to this event**

===============================  ===============
Listener Class Name              Priority
===============================  ===============
`RefuseNotificationSubscriber`_  0
===============================  ===============

``pimee_workflow.product_draft.pre_remove``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **before** a product draft has been removed.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

``pimee_workflow.product_draft.post_remove``
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This event is dispatched **after** a product draft has been removed.

**Event Class**: `GenericEvent <http://api.symfony.com/2.7/Symfony/Component/EventDispatcher/GenericEvent.html>`_

**Built-in PIM subscribers registered to this event**

================================  ===============
Listener Class Name               Priority
================================  ===============
`RemoveNotificationSubscriber`_   0
================================  ===============

.. _SendForApprovalSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/SendForApprovalSubscriber.php
.. _ApproveNotificationSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/ApproveNotificationSubscriber.php
.. _RefuseNotificationSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/RefuseNotificationSubscriber.php
.. _RemoveNotificationSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/ProductDraft/RemoveNotificationSubscriber.php
.. _DetachProductPostPublishSubscriber: https://github.com/akeneo/pim-enterprise-dev/blob/master/src/PimEnterprise/Bundle/WorkflowBundle/EventSubscriber/PublishedProduct/DetachProductPostPublishSubscriber.php
