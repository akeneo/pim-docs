How to Define Access Control List
=================================

The access control list is editable on each user's role.

Define on each Controller Actions
---------------------------------

You can define a resource for the controller itself and for each action as in the following example:

.. code-block:: php

    use Oro\Bundle\UserBundle\Annotation\Acl;

    /**
     * @Acl(
     *      id="pim_catalog_product",
     *      name="Product manipulation",
     *      description="Product manipulation",
     *      parent="pim_catalog"
     * )
     */
    class ProductController extends AbstractDoctrineController
    {
        /**
         * List product attributes
         *
         * @param Request $request the request
         *
         * @Acl(
         *      id="pim_catalog_product_index",
         *      name="View product list",
         *      description="View product list",
         *      parent="pim_catalog_product"
         * )
         * @return Response
         */
        public function indexAction(Request $request)
        {
        }
    }

Here, a resource pim_catalog_product with a child pim_catalog_product_index will be created when the command is run:

.. code-block:: bash

    $ php app/console oro:acl:load

Each time you access the related route, the system checks if you have the right permissions.

Check into a Twig Template
--------------------------

You can explicitely check if you're allowed to access to a resource in a a twig template:

.. code-block:: jinja

    {% if resource_granted('pim_catalog_product_create') %}


