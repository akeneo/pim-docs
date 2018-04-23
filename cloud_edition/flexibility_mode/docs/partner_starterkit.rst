Partners Starterkit
===================

After the Flexibility setup, you will need to take notice of the following points:

A. Deploy your ssh keys
-----------------------
To get access to the environments, you need to follow this procedure:

#. Make sure that the SSH keys you want to grant access are registered in the `Partners Portal`_
#. Create a new cloud ticket to the support with following items :

    - Name of your Akeneo Cloud Project (i.e. <my-project>)
    - List of the SSH keys owners (if you have several keys, you must put in your request the content of the right one) on the Partners Portal, and for each key the environment you want to provide access to (you may not want to give access to the production environment to everybody)
    - List of IP addresses from where the SSH communication will take place


B. Use SSH
----------

Now you can connect through ssh to your instance, follow this documentation `Environments access`_

C. Setup composer
-----------------
To be able to update Akeneo PIM, you must setup your `composer into instance`_

D. Akeneo info
------------------

We provide some information about `our own akeneo setup`_

E. Usefull commands
-------------------

We provide some aliases to do some `superuser tasks`_


.. _`Partners Portal`: https://partners.akeneo.com
.. _`Environments access`: ./environments_access.html
.. _`composer into instance`: ./composer_settings.html
.. _`our own akeneo setup`: ./pim_application.html
.. _`superuser tasks`: ./partners.rst.html

