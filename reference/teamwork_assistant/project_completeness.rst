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

We need to go deeper
____________________

We found two ways to deal with this problem. The first one was to calculate this detailed completeness for each user and
cache it in a table. The benefit of this method is clear, each time we need those numbers we simply query the database:
"Hey you, I just met Mary, what is her completeness? TODO: 42, IN_PROGRESS: 1251, DONE: 3254.". And this is crazy, the
result is instant, but the main drawback, is that the data is not up-to-date as this table is a cache. It needs to be
calculated as often as possible but the task is so huge that with many different projects and users a night could not
be sufficient.

One table to rule them all
__________________________

This first method does not seem to be the chosen one. Let's recap which data we need:
 - For each locale,
 - For each channel,
 - For each user,
 - Does the product
 - have at least an attribute filled in?
 - or is it complete?

Oh. Can you see this?

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

.. _calculation step: calculation_step.html

(*) As these fields are only used for the completeness calculation purpose and they have no sense from a business point
of view, they are not mapped to doctrine. The `products` field from project model is a n - n table that helps to join
project to pre-processing. The `user groups` field from project model is a n - n table that helps to know who is
impacted by the product selection. These tables are filled thanks to the `calculation step`_.

By cross-checking pre-processing table, project table, category accesses table, attribute group accesses table we are
able to calculate those three little numbers that are the number of products not started to enrich, in progress and done
in terms of a user and a products selection.

In ORM, the process of joining Project to Category access works as follows:

project -> project_products -> product_category -> category -> category_access

Problem comes with ODM storage where `product_category` is transferred to MongoDB. Joining all these tables in MySQL is
not possible anymore. To avoid to do a lot of extra queries in MongoDB to fetch `product_category`, we added
`PimEnterprise\\Component\\ActivityManager\\Job\\ProjectCalculation\\CalculationStep\\LinkProductAndCategoryStep`
`calculation step`_. This step allows us to re-create this link table in MySQL and be able to calculate every
completeness with MySQL even in ODM.

This is the end
_______________

Still the drawback mentioned in the first method remains: the completeness is not up-to-date.

However, the advantage is that the pre-processing of all data is really faster than calculating and caching the full
completeness per user. Guessing impacted user groups is not a big deal and can be done during project creation to gain
completeness calculation time and scalability.

According to our benchmark on a catalog with 3.6 millions of product values, pre-processing those data is feasible
during the night for many projects as we don't pre-process all the catalog but only products concerned by projects.
As it's faster, the regularity of the pre-processing data updating can be adjusted as required.
