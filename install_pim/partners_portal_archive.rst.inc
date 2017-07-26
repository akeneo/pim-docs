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

Getting Enterprise Edition access via the Partners Portal
*********************************************************
 to retrieve all open source packages and their updates.
To download the PIM Enterprise Edition, you have to get access to our private enterprise edition repository by sending your SSH public key to our systems.

Login to the `Partners Portal <https://partners.akeneo.com/login>`_:

* In your account go to manage public keys

.. image:: ./papo/manage_public_keys.png

* Click the following button

.. image:: ./papo/add_public_key.png

* Enter a name, an email and the associated public key file content (usually located at `~/.ssh/id_rsa.pub`) and click save

.. image:: ./papo/add_public_key_2.png

Requesting an archive on the Partners Portal
********************************************
In the left menu click on Activities > Project and then on your project.

On your project page on the right you will find a button to download the archive. We strongly recommend you to choose the latest version.

.. image:: ./papo/download_archive.png
