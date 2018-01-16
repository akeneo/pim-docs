Partners
========

| On Flexiblity Mode, you also have access to custom aliases.
| Those one allowed you to run commands with higher privileges like starting/restarting system daemons.
|
| Below the list of aliases:

+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| Alias                      | Argument                 | Action                                                                                                                                     |
+============================+==========================+============================================================================================================================================+
| ``partners_mysql``         | ``status|start|restart`` | Show status or start/restart mysql daemon                                                                                                  |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_elasticsearch`` | ``status|start|restart`` | Show status or start/restart elasticsearch daemon                                                                                          |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_php7.1-fpm``    | ``status|start|restart`` | Show status or start/restart php-fpm daemon (Command name can vary depending on php version)                                               |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_supervisor``    | ``status|start|restart`` | Show status or start/restart supervisor daemon (Used for php daemon queue/pim batch)                                                       |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
| ``partners_clear_cache``   |                          | Clear properly PIM's cache (doctrine). Stop php-fpm and supervisor, delete PIM's cache folder, warmup PIM and start php-fpm and supervisor |
+----------------------------+--------------------------+--------------------------------------------------------------------------------------------------------------------------------------------+
