Getting Akeneo Partners Portal Access
*************************************
Akeneo Partners Portal login credentials are sent to you by email. If case you haven't received it you can request one at: http://helpdesk.akeneo.com/

It’s important to know that there’re 2 types of users that can do this action:

* Supervisor
* Developers

Only Supervisors can create new Developers (user assigned to Developer group) by going to My Company > Contacts > Add Contact > Saving > Activating it (by clicking on the red X).

Adding Permissions into the Project
***********************************
Supervisor user has to add the allowed user to get the bootstrap into the Permission Box under the Project.

* Go to Activities menu, Projects
* Choose the corresponding project
* Edit the permission box with the white pen in a blue button

.. image:: ./papo/permission_box.png

* Add contact

.. image:: ./papo/permission_add.png

* Save (it will send an email to the given users letting them know that their access to the project is ready)

.. note::

   Only Users created under the account, with a Supervisor or a Developer profile are going to be listed.

If you are not a Supervisor, you should contact that person to be added under the permissions. You can check who the Supervisor is in your company by going in My Company > General Information and under the section Supervisors you will see them listed.

Sharing your Public SSH Key
***************************
Each user wanting to get the bootstrap archive has to share their SSH public key with Akeneo first.

* Login to the `Partners Portal <https://partners.akeneo.com/login>`_:

* Click over you User name in the upper right corner.
 
* Select Manage Public Keys

.. image:: ./papo/manage_public_keys.png

* Click Add a new public key

.. image:: ./papo/add_public_key.png

* Give the key a name, an email and the associated public key file content (usually located at `~/.ssh/id_rsa.pub`) 
 
* Click save

.. image:: ./papo/add_public_key_2.png

Requesting an Archive on the Partners Portal
********************************************
Once the users are authorized under the Permissions in the project, they will be able to see the download button on the project.

* Go to Projects under Activities menu.
* Choose the corresponding project.
* Look for the Version button on the right side of the screen.
* Click on it, you’ll be prompted to select one PIM EE version.

.. image:: ./papo/download_button.png
 
Once this is done, you should see a completeness message. Bootstrap archive will be sent to your email in the following 5 minutes. Sometimes it could take a bit more, but always check your Spam folder before requesting the bootstrap again.

.. warning::

   We strongly recommend you to choose the latest version.

