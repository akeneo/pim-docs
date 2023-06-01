Security best practices
=========================

Avoid information leakage
#############################

Rather than directly attacking a server, attackers will first run discovery tasks to steal publicly accessible data off the server.

There are many types of sensitive information that you should protect from attackers, including system data, configuration, secrets, 
intellectual property and an individual's personal (private) information.

You should never enable these in production environments as it will lead to major security vulnerabilities in your project.
- `adminer <https://www.adminer.org/>`_
- `Php Symfony Profiler <https://symfony.com/doc/current/profiler.html>`_

On test/dev/staging/pre-prod environments, if needed only, make sure to ask support team to add with authorized IPs
using Apache.

.. code-block:: apacheconf
	:linenos:

	<Location /adminer.php>
		Require all denied
	</Location>

	<Location /app_dev.php>
		Require all denied
	</Location>
