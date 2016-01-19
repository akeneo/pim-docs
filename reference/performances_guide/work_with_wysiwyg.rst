Work with WYSIWYG :
-------------------

WYSIWYG are javascript components and so are executed on the client side. Hardware performances of the client can affect the speed of pages with WYSIWYG.

That's why it's not recommended to have more that one hundred WYSIWYG in the same page. In the product edit form, only attributes in the
current attribute group and the current scope are rendered. It means that you should not have more than one hundred WYSIWYG in the same
attribute group and scope. When configuring mass edit common attributes or editing variant group attributes, all scopes are rendered at
the same time. It means you should not add more than one hundred WYSIWYG all scopes included at once.

For example, a product with 2 attribute groups, 100 scopable WYSIWYG in each group and 5 scopes, has 1000 WYSIWYG. You can render them
in the PEF because you will render WYSIWYG 100 by 100. But in variant groups and mass edit, you should not add more than 20 WYSIWYG because
they have 5 scopes and 100 WYSIWYG will be rendered.
