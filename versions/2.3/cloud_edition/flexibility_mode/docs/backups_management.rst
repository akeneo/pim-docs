Backups management
==================

Saving
------

| Saving is done in standard dump for the database (mysqldump for mysql) and tar.gz for files.
| Backups include both data, assets (if stored locally) and the full code of your Akeneo instance, including extensions & custom code.
|
| The backup frequency is every day.
| The backup retention policy is the following one:

    - 7 last days,
    - 5 last weeks,
    - 12 last months,
    - 10 last years

| Backups are saved on Google Cloud Storage in the same region than your environments.
| We also save backups on a different datacenter each weeks.
|

Restoring
---------

| There is no environment settings inside the database so the dump can be restored as well.
| The backup can only be restored by Akeneo on the production environment.
|
