About Log4Shell Vulnerability
=============================

Security Update
***************

As you are an Akeneo Enterprise Edition PaaS (ie, Flexibility) user in a v3.2, or v4.0, we will need to take an extra precaution to better protect your version (SaaS and v5 customers are not required to take this action as it is resolved natively) due to the Apache Log4j Java critical security vulnerability that was disclosed Friday last week.

Additional information
**********************

You may have heard about a global critical vulnerability disclosed Friday last week, which has the potential to affect a lot of online services and companies. This vulnerability comes from a Java logging library.
Just after the publication of this vulnerability (CVE-2021-44228, or Log4shell), we closely examined any related impacts to Akeneo, assessed our exposure, and came to the following statement:

1) As PHP applications, our main software is not impacted;
2) We use Elasticsearch (from Elastic) as a technical component and in a version that is slightly impacted by this vulnerability

Action Items -- Please Take Notice
**********************************

Akeneo PIM Enterprise Edition PaaS (ie, Flexibility) customers with versions below v5 are not impacted by the main issue, which is remote code execution.

But they may be exposed to technical information leakage, (NOT business data), with the mitigation that the PIM is protected by user authentication, and this technical information leakage cannot be done without being connected to the PIM with a valid user.

While the risk is pretty low, we decided to upgrade our platform. This will generate a very small downtime of less than 30s.

The maintenance window will occur on Friday, December the 17th, between 9am GMT and 9:30am GMT.

Please Take Action
******************

Users & API may get 500 errors during the upgrade. Long-running operations (import, export, mass actions, etc.) will fail if they are running at the same time we are executing the upgrade.
We recommend that you avoid running long operations during the maintenance slot.
