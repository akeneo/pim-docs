Data File Transfer
==================

| Depending on your project needs, you may have to transfer data files from/to Akeneo Cloud Environment.
|
| Akeneo Flexibility can provide dedicated SFTP access to the environments.
| This SFTP access is independent from the SSH access used by the integrator to deploy custom code.
|
| The SFTP access uses the **akeneosftp** user in a chroot.
|
| The chroot is accessible by the akeneo user (so by the PIM application too) from the **/data/transfer/pim** directory on the environment.
