Akeneo style guide
==================

The style guides are automatically generated in a single page index.php.
When you update a BEM module in the pim-community-dev repository, you have to update the style guide.

How it works
------------

- The file `styleguide/config.yml` contains the list of the available module, their description and modifiers.
- Each module has its own folder in `templates/`. The default template is called `base.html.twig`.
- You can simply access to `styleguide/index.php` to view the style guides.

Add a new module modifier
-------------------------

If you added a modifier --bar to the module AknFoo, you have to update `styleguide/config.yml` to add a new modifier,
specifying its new class `AknFoo--bar`.

Add a new element modifier
--------------------------

If you added a modifier --baz to the element AknFoo-bar, you have to:

- Read the file `AknFoo/base.html.twig` to check this exists:
  `<div class="AknFoo-bar{% if barClass is defined %} {{ barClass }}{% endif %}">`
  If not, simply update the HTML to allow this option.
- Update the `styleguide/config.yml` to add a modifier, with the option `{ barClass: 'AknFoo-bar--baz' }`.

Add a new template for an existing module
-----------------------------------------

Sometimes, some modules need to have another template than the default one (e.g. AknButton). Simply create a new
template `templates/AknFoo/newTemplate.html.twig`, then add a new modifier with `{ template: 'newTemplate.html.twig' }`.

Add a new module
----------------

- Create a new base template in `templates/AknFoo/base.html.twig`.
- Add your new module in the right category in `styleguide/config.yml`.
- Refer to the documentation above to add modifiers.
