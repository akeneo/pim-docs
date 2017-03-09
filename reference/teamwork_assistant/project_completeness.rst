What is the project completeness?
=================================

Overview
________

In this reference guide you will learn what the project completeness refers to and how we manage it in Akeneo PIM. It
will explain our technical and structure choices.

Goal
____

The Teamwork Assistant feature is about creating projects from a product selection that gives a vision on the enrichment
progress to contributors (users in charge of the enrichment). Each project gives to the contributor their own
completeness on the product selection, taking into account their permissions to edit or not product, attribute groups,
locales and channels.

For example, if Bob can only edit the "Technical" attribute group of the "High-tech" category, he will see the project
100% complete if all attributes in the attribute group "Technical" of "High-tech" products in the project are filled in,
even if other attribute groups are empty and required by the family. However, Julia, the owner of the project, will see
that it is not complete if some attributes are not filled in in others attribute groups. The contributor sees the
completeness of the project in terms of his own permissions, whereas the owner sees the completeness of all the project.

As every user will have his very own completeness depending on his permissions, we need to walk through every product
to compute completeness for every user. As you can guess, it's a really huge task that cannot be done synchronously.

One table to rule them all
__________________________

From this introduction, let's recap which data we need:
 - For each locale,
 - For each channel,
 - For each user,
 - Does the product
 - have at least an attribute filled in?
 - or is it complete?

Creating a table to gather all data we need to compute the completeness seems to be a good idea but we need some
adjustments. It can't be scalable if we do this for each user.

.. _format: format.html

So let's remove users from this table. And now, how can we retrieve the completeness for a user? In the project format_,
we have product filters. From those filters it's easy to know which user is impacted by the product selection. To gain
some time, we pre-calculate which user is impacted and we link them to project at its creation. But it's not scalable
either. If we add a user, its completeness could not be calculated because he is not linked to the project yet. The
problem is resolved by linking user groups to project and not directly to users.

Some data are still missing. User groups permissions are not only about products but also about attribute groups.

Here is the final structure we needed (only data that are relevant for the demonstration are shown):
 - Pre-processing table:

  - For each locale,
  - For each channel,
  - Does the attribute group,
  - in the product,
  - have at least an attribute filled in?
  - or is it complete?

 - Project model:
 
  - locale
  - channel
  - product filters
  - products (*)
  - user groups (*)

.. _calculation step: project_creation.html#calculation-steps

(*) As these fields are only used for the completeness calculation purpose and they have no sense from a business point
of view, they are not mapped to doctrine. The `products` field from project model is a n - n table that helps to join
project to pre-processing. The `user groups` field from project model is a n - n table that helps to know who is
impacted by the product selection. These tables are filled thanks to the `calculation step`_.

By cross-checking pre-processing table, project table, category accesses table, attribute group accesses table we are
able to calculate those three little numbers that are the number of products not started to enrich, in progress and done
in terms of a user and a products selection.

In ORM, the process of joining Project to Category access works as follows:

``project -> project_products -> product_category -> category -> category_access``

Problem comes with ODM storage where `product_category` is transferred to MongoDB. Joining all these tables in MySQL is
not possible anymore. To avoid to do a lot of extra queries in MongoDB to fetch `product_category`, we added
``PimEnterprise\\Component\\TeamworkAssistant\\Job\\ProjectCalculation\\CalculationStep\\LinkProductAndCategoryStep``
`calculation step`_. This step allows us to re-create this link table in MySQL and be able to calculate every
completeness with MySQL even in ODM.

Project Completeness Format
___________________________

One normalized the completeness looks like this:

.. code-block:: php

    array:7 => [
      "isComplete" => (bool),
      "productsCountTodo" => (int),
      "productsCountInProgress" => (int),
      "productsCountDone" => (int),
      "ratioTodo" => (int),
      "ratioInProgress" => (int),
      "ratioDone" => (int),
    ]

This is the end
_______________

The drawback of this method is that the completeness is not really up-to-date. For the moment, the pre-processing data
are computed once you save a project, when you save a product concerned by a project from the Product Edit Form and the
sequential edit. Moreover, a command ``php app/console pimee:project:recalculate`` is provided to help you to
recalculate data according to your needs.

The advantage is that the regularity of the pre-processing data updating can be adjusted as required with this command.
Before to use this you should have a look to the scalability guide.

According to our benchmark on a catalog with 3.6 millions of product values, pre-processing those data is feasible
during the night for many projects as we don't pre-process all the catalog but only products concerned by projects and
products that as been updated.
