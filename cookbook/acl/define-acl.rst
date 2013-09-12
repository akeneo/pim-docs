How to define Access Control List
=================================

The access control list is editable on each user's role.

Define on each controller actions
---------------------------------

You can define a resource on the controller itself and on each action as in the following exemple :

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

Here, a resource pim_catalog_product with a child pim_catalog_product_index will be created when you'll run the command :

.. code-block:: bash

    $ php app/console oro:acl:load

Each time you access to the related route, the system check if you're granted to access.

Check into a twig template
--------------------------

You can explicitely check if you're granted to access to a resource in a a twig template :

.. code-block:: jinja

    {% if resource_granted('pim_catalog_product_create') %}


