Backups management
==================

A snapshot of your instance is made regulary and can be restored upon your request. 

.. note::

    Only production environments can be restored

Backup frequency
****************

- 1 per hour for 24 hours
- 1 per day for the last 7 days 
- 1 per week for the last 5 weeks
- 1 per months for the last 12 months


.. note::  
    
    Backups are stored on Google Cloud Storage on the same region as your environments. We also save backups on a different datacenter each weeks.
