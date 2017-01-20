Project Format
==============

.. code-block:: php

    array:8 => [
      "label" => "Summer collection 2016"
      "code" => "summer-collection-2016"
      "description" => "Our summer collection 2016 is ready to enrich."
      "due_date" => "2017-01-27"
      "owner" => [] User to [internal_api] format
      "channel" => [] Channel to [internal_api] format,
      "locale" => [] Locale to [internal_api] format,
      "datagridView" => [] DatagridView [internal_api] format
    ]

Project Completeness Format
===========================

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
