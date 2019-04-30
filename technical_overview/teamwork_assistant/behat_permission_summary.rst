Users permission summary for Behat tests
========================================

.. _Setup Behat part: /technical_architecture/best_practices/core/behat.html

In the case you extended the Teamwork Assistant, you may want to check that you didn't broke basics features. You can
have a look to the `Setup Behat part`_ to learn more about how to run our functional tests.

Because the Teamwork Assistant heavily uses permissions, we created a custom catalog that we use in our test features.
Here is a summary about permissions we use in our catalog. This catalog has been modified in few scenario but it is
still mainly the same all over tests.

Users:
------
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+
| USERNAME | Full name        | Role                 | User Group                                         | Description                    |
+==========+==================+======================+====================================================+================================+
| admin    | John Doe         | ROLE_ADMINISTRATOR   | Read Only                                          | -                              |
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+
| Julia    | Julia Stark      | ROLE_CATALOG_MANAGER | Marketing, Technical Clothing, Technical High-Tech | -                              |
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+
| Marc     | Marc Assin       | ROLE_CATALOG_MANAGER | Technical Clothing, Technical High-Tech            | Technical manager              |
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+
| Mary     | Mary Smith       | ROLE_USER            | Marketing                                          | -                              |
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+
| Claude   | Claude Yachifeur | ROLE_USER            | Technical Clothing                                 | Technical Clothing redactor    |
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+
| Teddy    | Teddy Ferant     | ROLE_USER            | Technical High-Tech                                | Technical High-Tech redactor   |
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+
| Kathy    | Kathy Peneflame  | ROLE_USER            | Media manager                                      | Media manager                  |
+----------+------------------+----------------------+----------------------------------------------------+--------------------------------+

Permissions summary:
--------------------
+----------+--------------------------------------------------------+---------------------------------------------------+
| USERNAME | Categories                                             | Attribute groups                                  |
+==========+========================================================+===================================================+
| admin    | Read only                                              | Read only                                         |
+----------+--------------------------------------------------------+---------------------------------------------------+
| Julia    | Edit on all                                            | Edit on all                                       |
+----------+--------------------------------------------------------+---------------------------------------------------+
| Marc     | Edit Clothing & High-Tech / Can't see Decoration       | Read Marketing & Media / Edit Technical & Others  |
+----------+--------------------------------------------------------+---------------------------------------------------+
| Mary     | Edit Clothing & High-Tech & Decoration                 | Edit Marketing & Others & Media / Read Technical  |
+----------+--------------------------------------------------------+---------------------------------------------------+
| Claude   | Edit Clothing / Read High-Tech  / Can't see Decoration | Edit Technical & Others & Media / Read Marketing  |
+----------+--------------------------------------------------------+---------------------------------------------------+
| Teddy    | None on Clothing & Decoration / Edit High-Tech         | Edit Technical & Others & Media / Read Marketing  |
+----------+--------------------------------------------------------+---------------------------------------------------+
| Kathy    | Edit Clothing & High-Tech & Decoration                 | Edit Media / Read Marketing / Can't see Technical |
+----------+--------------------------------------------------------+---------------------------------------------------+
