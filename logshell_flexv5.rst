About Log4Shell Vulnerability for Flexibility v5 Users
======================================================

Security Update
***************

As you are an Akeneo Enterprise Edition PaaS (ie, Flexibility) v5 user, we have good news for you. Akeneo Enterprise Edition PaaS (ie, Flexibility) v5 users are NOT impacted by the Apache Log4j Java critical security vulnerability that was disclosed on Friday, December the 10th 2021. No action is needed from you and your Akeneo instance remains secure.

Additional information
**********************

You may have heard about a global critical vulnerability disclosed on Friday, December the 10th 2021, which has the potential to affect a lot of online services and companies. This vulnerability comes from a Java logging library.
Just after the publication of this vulnerability (CVE-2021-44228, or Log4shell), we closely examined any related impacts to Akeneo, assessed our exposure, and came to the following statement:

1. As PHP applications, our main software is not impacted;
2. We use Elasticsearch (from Elastic) as a technical component and the version used on Serenity, Growth Edition and Flexibility v5 is not impacted by this vulnerability


If you are an on-premise user
*****************************

If you followed the technical requirements for PIM v5.0, you are not impacted by the vulnerability, as the required Elasticsearch version for PIM v5 is not vulnerable.
