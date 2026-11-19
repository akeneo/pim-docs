How to Define Access Control List
=================================

Access control is composed of two steps:

- Creating ACL resources
- Enforcing the permissions


Creating ACL resources
----------------------

You can create two types of ACL resources: **action** and **entity**.

There are two ways of defining the ACL:

1. In ``YourCustomBundle/Resources/config/acl.yml``

    .. code-block:: yaml

        # YourCustomBundle/Resources/config/acl.yml
        your_custom_entity_edit:
            type: entity
            class: YourCustomBundle:CustomEntity
            permission: EDIT

        your_custom_action:
            type: action
            label: Your custom action

    .. code-block:: php

        // YourCustomBundle/Controller/CustomController.php
        use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

        class CustomController extends AbstractDoctrineController
        {
            /**
             * @AclAncestor("your_custom_entity_edit")
             */
            public function editAction()
            {
            }

            /**
             * @AclAncestor("your_custom_action")
             */
            public function customAction()
            {
            }
        }


2. Directly in the controller using annotations

    .. code-block:: php

        // YourCustomBundle/Controller/CustomController.php
        use Oro\Bundle\SecurityBundle\Annotation\Acl;

        class CustomController extends AbstractDoctrineController
        {
            /**
             * @Acl(
             *      id="your_custom_entity_edit",
             *      type="entity",
             *      class="YourCustomBundle:CustomEntity",
             *      permission="EDIT"
             * )
             */
            public function editAction()
            {
            }

            /**
             * @Acl(
             *      id="your_custom_action",
             *      type="action",
             *      label="Your custom action"
             * )
             */
            public function customAction()
            {
            }
        }

For a more complete explanation of the ACL options, refer to `OroSecurityBundle`_.

.. _OroSecurityBundle: https://github.com/oroinc/platform/tree/master/src/Oro/Bundle/SecurityBundle


Enforcing the permissions
-------------------------

Controller actions with @Acl or @AclAncestor annotations are already protected.
To allow conditional access to other resources, you can either use the ``SecurityFacade`` component
or enforce permissions directly in templates.

- Using ``SecurityFacade``:
    .. code-block:: yaml

        # YourCustomBundle/Resources/config/services.yml
        your_custom.controller.custom:
            class: YourCustomBundle\Controller\CustomController
            parent: pim_catalog.controller.abstract_doctrine
            calls:
                - [ setSecurityFacade, ['@oro_security.security_facade'] ]

    .. code-block:: php

            // YourCustomBundle/Controller/CustomController.php
            use Oro\Bundle\SecurityBundle\SecurityFacade;

            class CustomController extends AbstractDoctrineController
            {
                private $securityFacade;

                public function setSecurityFacade(SecurityFacade $securityFacade)
                {
                    $this->securityFacade = $securityFacade;
                }

                public function removeAction()
                {
                    if ($this->securityFacade->isGranted('your_custom_action')) {
                        // Access is granted, execute the custom action
                    }
                }
            }

- In Twig templates:
    .. code-block:: jinja

        {% if resource_granted('your_custom_action') %}
            {# Some protected content here #}
        {% endif %}
