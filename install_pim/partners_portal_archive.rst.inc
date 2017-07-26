Getting Akeneo Partners Portal Access
*************************************
Akeneo Partners Portal login credentials are sent to you by email. If case you haven't recieved it you can request one at: http://helpdesk.akeneo.com/

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
* Add new Users
  * Only Users created under the account, with a Supervisor or a Developer profile are going to be listed.
* Save
  * This going to send an email to the given users letting them know that their access to the project is ready.

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

Requesting an archive on the Partners Portal
********************************************
In the left menu click on Activities > Project and then on your project.

On your project page on the right you will find a button to download the archive. We strongly recommend you to choose the latest version.

.. image:: ./papo/download_archive.png
