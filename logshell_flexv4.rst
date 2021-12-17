About Log4Shell Vulnerability for Flexibility users below v5.0
==============================================================

Security Update
***************

As you are an Akeneo Enterprise Edition PaaS (ie, Flexibility) user in a version prior to v5.0, we took extra precaution to better protect your version. We applied a mitigation on Friday, December the 17th, around 9:30am GMT.

Your version is now protected against this vulnerability.

No action is required on your side.

Feel free to contact your Akeneo Customer Success Manager if you have any question or need additional information on this subject.

Additional information
**********************

You may have heard about a global critical vulnerability disclosed Friday, December the 10th, 2021, which has the potential to affect a lot of online services and companies. This vulnerability comes from a Java logging library.
Just after the publication of this vulnerability (CVE-2021-44228, or Log4shell), we closely examined any related impacts to Akeneo, assessed our exposure, and came to the following statement:

1. As PHP applications, our main software is not impacted
2. We use Elasticsearch (from Elastic) as a technical component. We applied a specific configuration to be protected against the aforementioned configuration.


If you are an on-premise user
*****************************

Please follow the Elasticsearch vendor advisory available here:
https://discuss.elastic.co/t/apache-log4j2-remote-code-execution-rce-vulnerability-cve-2021-44228-esa-2021-31/291476

and apply the recommended mitigation (setting the "log4j2.formatMsgNoLookups" JVM option to "true").
