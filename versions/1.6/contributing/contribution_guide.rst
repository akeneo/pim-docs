Submit a Patch to the Core
==========================

As Akeneo PIM is an open-source project, all contributions are very welcome!

Note that documentation has been largely inspired from the `Sylius documentation`_. Thank them for the great work.

If you don't know how to start, you can pick an issue here: https://github.com/akeneo/pim-community-dev/labels/wanna-contribute

Step 0: Sign the Contributor Agreement
--------------------------------------

To be able to merge your contribution, we need you to read and sign the following contributor agreement http://www.akeneo.com/contributor-license-agreement/

Step 1: Set up your Environment
-------------------------------

Install the Software Stack
~~~~~~~~~~~~~~~~~~~~~~~~~~

Before working on Akeneo PIM, set a Symfony2 friendly environment up with the following software:

* Git
* Follow the technical requirements :doc:`/developer_guide/installation/system_requirements/system_requirements`

Configure Git
~~~~~~~~~~~~~

Set your user information up with your real name and a working email address:

.. code-block:: bash

    $ git config --global user.name "Your Name"
    $ git config --global user.email "you@example.com"

.. tip::

    If you are new to Git, you are highly recommended to read the excellent and
    free `ProGit`_ book.

.. tip::

    If your IDE creates configuration files inside the directory of the project,
    you can use global ``.gitignore`` file (for all projects) or
    ``.git/info/exclude`` file (per project) to ignore them. See
    `Github's documentation`_.

.. tip::

    Windows users: when installing Git, the installer will ask what to do with
    line endings, and will suggest replacing all LF with CRLF. This is the wrong
    setting if you wish to contribute to Akeneo PIM. Selecting the as-is method is
    your best choice, as Git will convert your line feeds to the ones in the
    repository. If you have already installed Git, you can check the value of
    this setting by typing:

    .. code-block:: bash

        $ git config core.autocrlf

    This will return either "false", "input" or "true"; "true" and "false" being
    the wrong values. Change it to "input" by typing:

    .. code-block:: bash

        $ git config --global core.autocrlf input

    Replace --global by --local if you want to set it only for the active
    repository

Get the Akeneo PIM Source Code
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Get the Akeneo PIM source code:

* Create a `GitHub`_ account and sign in;

* Fork the `Akeneo PIM repository`_ (click on the "Fork" button);

* After the "forking action" has completed, clone your fork locally
  (this will create a ``pim-community-dev`` directory):

.. code-block:: bash

      $ git clone git@github.com:USERNAME/pim-community-dev.git

* Add the upstream repository as a remote:

.. code-block:: bash

      $ cd pim-community-dev
      $ git remote add upstream git://github.com/akeneo/pim-community-dev.git

Step 2: Work on your Patch
--------------------------

The License
~~~~~~~~~~~

Before you start, you must know that all patches you are going to submit
must be released under the *OSL-3.0 license*, unless explicitly specified in your
commits.

Create a Topic Branch
~~~~~~~~~~~~~~~~~~~~~

Each time you want to work on a patch for a bug or on an enhancement, create a
topic branch:

.. code-block:: bash

    $ git checkout -b BRANCH_NAME master

.. tip::

    Use a descriptive name for your branch (``issue_XXX`` where ``XXX`` is the
    GitHub issue number is a good convention for bug fixes).

The checkout command above automatically switches the code to the newly created
branch (you can check the branch you are working on with ``git branch``).

Work on your Patch
~~~~~~~~~~~~~~~~~~

Before working on a contribution for an Akeneo repository, please read the following `code conventions`_
and `coding standard`_ to make sure you respect all our standards.

When you work on a patch, please keep in mind:

* For a bug fix contribution, please avoid any BC breaks. If a BC break can't be avoided add a comment and detail why.

* For all contributions, tests are as important as business code.

    - Behavior of the application has to be tested with Behat.
    - Behavior of the business code has to be tested with PHPSpec.

.. note::

    We wrote a guide to `setup behat`_ in Akeneo PIM and you can check the `behat quick intro`_ on their documentation.

    Here is the documentation to `begin with PHPSpec`_ and `Prophecy documentation`_.

Commit your code
----------------

Begin by adding file content to your index

.. code-block:: bash

    $ git add -p

This will run a `git add` command with an interactive mode. You'll be able to choose which chunk of code you want to add.

Then you have to create one or several commits of your code

.. code-block:: bash

    $ git commit

* Create atomic and logical commits with a relevant message.

* Squash irrelevant commits that are just about fixing coding standards or fixing typos in your own code.

* Never fix coding standards in some existing code as it makes the code review
  more difficult (submit CS fixes as a separate patch).

It will help us to:
 - Speed up the reviewing process
 - Revert a single commit if needed
 - Cherry pick a commit if needed

Example of a well formed commit message (from github doc https://git-scm.com/book/ch5-2.html)

.. note::

    Short (50 chars or less) summary of changes

    More detailed explanatory text, if necessary.  Wrap it to
    about 72 characters or so.  In some contexts, the first
    line is treated as the subject of an email and the rest of
    the text as the body.  The blank line separating the
    summary from the body is critical (unless you omit the body
    entirely); tools like rebase can get confused if you run
    the two together.

    Further paragraphs come after blank lines.

      - Bullet points are okay, too

      - Typically a hyphen or asterisk is used for the bullet,
        preceded by a single space, with blank lines in
        between, but conventions vary here

Prepare your Patch for Submission
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When your patch is about a bug fix and we give you a reference to a ticket ``PIM-xxxx``.
You have to add to the ``CHANGELOG-1-x.md`` file (1-x is the version of the PIM you want to contribute)
under the BUG FIXES step of the next version the reference to the ticket and a description of the bug fix.

Then, if you introduced BC Breaks in namespaces for example (but you should/must not), under the BC BREAK step add a
description of the BC Break.
Moreover, you have to add in ``UPGRADE-1.x.md`` a way to fix this BC Break in files (like sed command for example).
To finish, if you introduced database BC Break, you have to add migration files in `upgrades/schema/`.
In most of the cases using `php app/console doctrine:migrations:diff` is enough to create a database migration class
(see `Doctrine migration documentation`_) but sometimes you will have to do it manually.

When your patch is not about a bug fix (when you add a new feature or change
an existing one for instance), it must also include the following:

* A short explanation of the new feature in the relevant ``CHANGELOG`` file

* Same rule as bug fixes for the BC Break concern.

* An explanation on how to upgrade an existing application in the relevant
  ``UPGRADE`` file(s) if the changes break backward compatibility or if you
  deprecate something that will ultimately break backward compatibility.

Step 3: Submit your Patch
-------------------------

Whenever you feel that your patch is ready for submission, follow the
following steps.

Rebase your Patch
~~~~~~~~~~~~~~~~~

Before submitting your patch, update your branch (needed if it takes you a
while to finish your changes):

.. code-block:: bash

    $ git checkout master
    $ git fetch upstream
    $ git merge upstream/master
    $ git checkout BRANCH_NAME
    $ git rebase master

When doing the ``rebase`` command, you might have to fix merge conflicts.
``git status`` will show you the *unmerged* files. Resolve all the conflicts,
then continue the rebase:

.. code-block:: bash

    $ git add ... # add resolved files
    $ git rebase --continue

Push your branch remotely:

.. code-block:: bash

    $ git push --force origin BRANCH_NAME

Make a Pull Request
~~~~~~~~~~~~~~~~~~~

You can now make a pull request on the ``Akeneo/pim-community-dev`` GitHub repository.

The pull request description must include the following checklist at the top
to ensure that contributions may be reviewed without needless feedback
loops and that your contributions can be included into Akeneo PIM as quickly as
possible:

.. code-block:: text

    | Q                                 | A
    | --------------------------------- | ---
    | Added Specs                       | [yes|no]
    | Added Behats                      | [yes|no]
    | Changelog updated                 | [yes|no]
    | Review and 2 GTM                  | [yes|no]
    | Migration scripts                 | [yes|no]
    | Tech Doc                          | [yes|no]

Some explanation for this Definition of Done :

* "Added Specs" means phpspec have been written, every class has its own PHPSpec or the existing one has been updated except controllers, form types, commands, doctrine entity (POPO), symfony semantic config.

* "Added Behats" means scenario have been written, for nominal and limit cases, internal api can also be tested through behat via commands (like query or updater).

* "Changelog updated" means the bug fix line has been added (in case of bug) via an explicit sentence, all the BC breaks (with the last minor version) have been listed and, in case of improvement (functional or technical), a short description (prefixed by the issue number).

* "Review and 2 GTM" means the technical review has been done, comments have been fixed and at least two teammates have given a Good To Merge (GTM). Update it just before you merge.

* "Migration scripts" means you changed the data model and you provided migration script allowing to migrate data from previous minor version to the upcoming one.

* "Tech Doc" means cookbook and reference doc has been written if needed.

If you just submitted your PR for a typo, an example could now look as follows:

.. code-block:: text

    | Q                                 | A
    | --------------------------------- | ---
    | Added Specs                       | no
    | Added Behats                      | no
    | Changelog updated                 | yes
    | Review and 2 GTM                  | no
    | Migration script                  | no
    | Tech Doc                          | no

If you just submitted your PR for a bug fix with some BC Breaks in database, an example could now look as follows:

.. code-block:: text

    | Q                                 | A
    | --------------------------------- | ---
    | Added Specs                       | yes
    | Added Behats                      | yes
    | Changelog updated                 | yes
    | Review and 2 GTM                  | no
    | Migration script                  | yes
    | Tech Doc                          | no

If some of the previous requirements are not met, create a todo-list and add
relevant items:

.. code-block:: text

    - [ ] Fix the specs as they have not been updated yet
    - [ ] Submit changes to the documentation
    - [ ] Document the BC breaks

If the code is not finished yet because you don't have time to finish it or
because you want early feedback on your work, add an item to todo-list:

.. code-block:: text

    - [ ] Finish the feature
    - [ ] Gather feedback for my changes

As long as you have items in the todo-list, please prefix the pull request
title with "[WIP]".

In the pull request description, give as much details as possible about your
changes (don't hesitate to give code examples to illustrate your points). If
your pull request is about adding a new feature or modifying an existing one,
explain the rationale for the changes. The pull request description helps the
code review.

In addition to this "code" pull request, you must also send a pull request to
the `documentation repository`_ to update the documentation when appropriate.

Rework your Patch
~~~~~~~~~~~~~~~~~

Based on the feedback on the pull request, you might need to rework your
patch. Before re-submitting the patch, rebase with ``upstream/master``, don't merge; and force the push to the origin:

.. code-block:: bash

    $ git rebase -f upstream/master
    $ git push --force origin BRANCH_NAME

.. note::

    When doing a ``push --force``, always specify the branch name explicitly
    to avoid messing other branches in the repo (``--force`` tells Git that
    you really want to mess with things so do it carefully).

Often, Akeneo team members will ask you to "squash" your commits. This means you will
convert many commits to one commit. To do this, use the rebase command:

.. code-block:: bash

    $ git rebase -i upstream/master
    $ git push --force origin BRANCH_NAME

After you type this command, an editor will popup showing a list of commits:

.. code-block:: text

    pick 1a31be6 first commit
    pick 7fc64b4 second commit
    pick 7d33018 third commit

To squash all commits into the first one, remove the word ``pick`` before the
second and the last commits, and replace it by the word ``squash`` or just
``s``. When you save, Git will start rebasing, and if successful, will ask
you to edit the commit message, which by default is a listing of the commit
messages of all the commits. When you are finished, execute the push command.

.. _`Akeneo PIM repository`:            https://github.com/akeneo/pim-community-dev
.. _ProGit:                             http://git-scm.com/book
.. _GitHub:                             https://github.com/signup/free
.. _`GitHub's Documentation`:           https://help.github.com/articles/ignoring-files
.. _`documentation repository`:         https://github.com/akeneo/pim-docs
.. _`Sylius documentation`:             http://docs.sylius.org/en/latest/contributing/index.html
.. _`code conventions`:                 http://docs.akeneo.com/latest/reference/best_practices/core/conventions.html
.. _`coding standard`:                  http://docs.akeneo.com/latest/reference/best_practices/core/standards.html
.. _`setup behat`:                      http://docs.akeneo.com/latest/reference/best_practices/core/behat.html
.. _`behat quick intro`:                http://docs.behat.org/en/v2.5/quick_intro.html
.. _`begin with PHPSpec`:               http://www.phpspec.net/en/latest/
.. _`Prophecy documentation`:           https://github.com/phpspec/prophecy#prophecy
.. _`Doctrine migration documentation`: http://docs.doctrine-project.org/projects/doctrine-migrations/en/latest/reference/introduction.html

Step 4: Is my pull request merged?
----------------------------------

Once your Pull Request is merged, don't hesitate to claim your badge "Core contributor" on badger at http://badger.akeneo.com/badge/41acec2c-649f-11e6-92dc-d60437e930cf
