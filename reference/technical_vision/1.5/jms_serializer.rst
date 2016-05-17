JMS Serializer [WIP]
====================

Since early version of the PIM, this library is required by Oro navigation.

As some point, we used JMS\Serializer\Annotation\Exclude to be able to fix issues with the serialization of our entities.

DONE:
 - Remove the use of annotations from entities

TODO:
 - Remove the JMS dependency from Oro\Bundle\NavigationBundle\Provider\TitleService
 - Remove the JMS dependency from Oro\Bundle\NavigationBundle\Title\StoredTitle
 - Remove the JMS dependency from Oro\Bundle\UIBundle\Twig\Md5Extension
 - Remove the "jms/serializer" and "jms/serializer-bundle"
