Composer settings
=================


| During the install process we used our own repository for the EE distribution. But you don't have any access to this repository after the install process.
| We setup your own distribution repository in the composer file, so you can change it if you want.


| To be able to pull from distribution, you must use a known ssh keypair from `the Portal <https://help.akeneo.com/portal/articles/get-akeneo-pim-enterprise-archive.html?utm_source=akeneo-docs&utm_campaign=composer_settings>`_.
| You can check your access rights to the distribution repository with this command:

.. code-block:: bash

    $ ssh git@distribution.akeneo.com -p 443
    akeneo@my-instance-staging:~$ ssh git@distribution.akeneo.com -p 443
    PTY allocation request failed on channel 0
    hello papo-full_name, this is git@distribution running gitolite3 3.6-1~bpo70+1 (Debian) on git 1.7.10.4

    R  	ElasticSearchBundle
    R 	    InnerVariationBundle
    R 	    LdapAuthenticationBundle
    R 	    pim-enterprise-dev-partner


| After that, you can set up your composer.json like that

.. code-block:: text

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/akeneo/pim-community-dev.git"
        },
        {
            "type": "vcs",
            "url": "ssh://git@distribution.akeneo.com:443/pim-enterprise-dev-<who_i_am>.git"
        }
    ],

| All details about Distribution System can be found in the `Portal section of the Help center <https://help.akeneo.com/portal/?utm_source=akeneo-docs&utm_campaign=composer_settings>`_.


Composer and github API rate limit
----------------------------------

| The Community edition have many branches/tags and during a composer update process, you can reach easily the github api rate limit. Without OAuth token, github use the ip to manage api rate limit, and to limit to 60 requests per hour (https://developer.github.com/v3/#rate-limiting).

| In order to update your pim, you must setup a github token for composer. You can run composer and follow the instructions to help you.

.. code-block:: bash

    $ composer update --prefer-dist --no-dev
    Loading composer repositories with package information
    Reading composer.json of akeneo/pim-community-dev (v2.0.2)
    Could not fetch https://api.github.com/repos/akeneo/pim-community-dev/commits/xxxxxxxxxxxxxxxxxxxxxxxxxxxxx, please create a GitHub OAuth token to go over the API rate limit
    Head to https://github.com/settings/tokens/new?scopes=repo&description=Composer+on+myinstance-2x+2018-02-23+1000
    to retrieve a token. It will be stored in "/home/akeneo/.composer/auth.json" for future use by Composer.
    Token (hidden):

