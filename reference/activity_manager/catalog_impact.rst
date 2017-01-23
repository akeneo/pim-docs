What Are The Impacts Of Catalog Updates On Projects?
====================================================

Several catalog updates can impact project integrity. In this case we need to remove the impacted projects.

Channel Updates
_______________

:Remove a channel:                 A project has to be removed if its channel is removed.
:Remove a locale from a channel:   A project has to be removed if its locale is now deactivated or if its locale is no
    longer part of its channel locales. This case happens when a locale is removed from a channel and both of them
    belong to a project.
:Remove a currency from a channel: A project is removed if it uses a currency as product filter that is removed from its
    channel. This case happens when a currency is removed from a channel that belongs to a project.

Attribute Updates
_________________

:Remove an attribute: A project must be removed if an attribute used as product filter is removed.

Category Updates
________________

:Remove a category: A project must be removed if a category used as product filter is removed.
