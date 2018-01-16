Environments Access
===================

| The system user dedicated to integrators on both environments is akeneo.
| To access the environment, you will the SSH command:

.. code-block:: shell

    ssh -A akeneo@my-project-staging.cloud.akeneo.com

| The **-A** option makes sure that your ssh-agent is forwarded to the server, meaning that you will get access to the Akeneo Entreprise repositories seamlessly.
|
| You may need to execute the **ssh-add** command previously, to add your key to the SSH Agent:

.. code-block:: shell

    ssh-add

For more information, please consult the following `Github documentation <https://developer.github.com/guides/using-ssh-agent-forwarding>`_
