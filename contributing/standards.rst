Coding Standards
================

When contributing code to Akeneo PIM, you must follow its coding standards (very close to Symfony Coding Standards).

Akeneo follows the standards defined in the `PSR-0`_, `PSR-1`_ and `PSR-2`_ documents.

Example
-------

Here is a short example containing most features described below:

.. code-block:: html+php

    <?php

    namespace Acme;

    /*
     * @author    FirstName LastName <email@domain.com>
     * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
     * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
    class FooBar
    {
        const SOME_CONST = 42;

        protected $fooBar;

        /**
         * @param string $dummy Some argument description
         */
        public function __construct($dummy)
        {
            $this->fooBar = $this->transformText($dummy);
        }

        /**
         * @param string $dummy Some argument description
         * @param array  $options
         *
         * @throws \RuntimeException
         *
         * @return string|null Transformed input
         *
         * @deprecated Will be removed in x.y
         */
        protected function transformText($dummy, array $options = [])
        {
            $mergedOptions = array_merge(
                [
                    'some_default'    => 'values',
                    'another_default' => 'more values',
                ],
                $options
            );

            if (true === $dummy) {
                return;
            }

            if ('string' === $dummy) {
                if ('values' === $mergedOptions['some_default']) {
                    return substr($dummy, 0, 5);
                }

                return ucwords($dummy);
            }

            throw new \RuntimeException(sprintf('Unrecognized dummy option "%s"', $dummy));
        }
    }

Structure
---------

* Add a single space after each comma delimiter;

* Add a single space around operators (``==``, ``&&``, ...);

* Put immutable entities on the left of comparison statements (``null === $var``, ``'string' === $this->test()``)

* Add a comma after each array item in a multi-line array, even after the
  last one;

* Add a blank line before ``return`` statements, unless the return is alone
  inside a statement-group (like an ``if`` statement);

* Use braces to indicate control structure body regardless of the number of
  statements it contains;

* Define one class per file - this does not apply to protected helper classes
  that are not intended to be instantiated from the outside and thus are not
  concerned by the `PSR-0`_ standard;

* Declare class properties before methods;

* Declare public methods first, then protected ones;

* Use parentheses when instantiating classes regardless of the number of
  arguments the constructor has;

* Exception message strings should be concatenated using :phpfunction:`sprintf`.

* If we expect something from a method/function (ie the returned value of the method/function is used by the caller) we should always do an explicit return (not ``return;``). 

Naming Conventions
------------------

* Use camelCase, not underscores, for variable, function and method
  names, arguments;

* Use underscores for option names and parameter names;

* Use namespaces for all classes;

* Prefix abstract classes with ``Abstract``.

* Suffix interfaces with ``Interface``;

* Suffix exceptions with ``Exception``;

* Use alphanumeric characters and underscores for file names;

* Don't forget to look at the more verbose :doc:`conventions` document for
  more subjective naming considerations.


Visibility
----------

Protected by default and public when necessary.

Useage of private is forbidden in the Core Components and Bundles.

We understand the advantages and know the drawbacks, we strictly follow this rule for now.

An interesting resource on this topic `private vs protected`_

.. _private vs protected: http://fabien.potencier.org/article/47/pragmatism-over-theory-protected-vs-protected

Documentation
-------------

* Add PHPDoc blocks for all classes, methods, and functions;

* Omit the ``@return`` tag if the method does not return anything;

* If your function returns an array of entity, use

    .. code-block:: php

        /**
         * @return string[]
         */

        /**
         * @return MyClass[]
         */

* If your function returns an ``ArrayCollection`` of entity, use

    .. code-block:: php

        /**
         * @return ArrayCollection of string
         */

        /**
         * @return ArrayCollection of MyClass
         */

* The ``@package`` and ``@subpackage`` annotations are not used.
* Write an ``use`` statement if the PHPdoc needs it, instead of writing the FQCN.

License
-------

* Akeneo PIM is released under the OSL license, and the license reference has to be
  present at the top of every PHP file, in the class PHPDoc.

* Some bundles as BatchBundle are released under the MIT licence, for these one, please follow the repository convention.

.. _`PSR-0`: http://www.php-fig.org/psr/psr-0/
.. _`PSR-1`: http://www.php-fig.org/psr/psr-1/
.. _`PSR-2`: http://www.php-fig.org/psr/psr-2/

