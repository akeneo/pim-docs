How to define Access Control List
=================================

The access control list is editable on each user's role.

On each controller actions
--------------------------

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
