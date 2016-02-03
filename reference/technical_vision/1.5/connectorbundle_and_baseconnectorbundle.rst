ConnectorBundle & BaseConnectorBundle [WIP]
===========================================

In 1.4, we re-worked the PIM import system and we've depreciated the old import system.

The new system has been implemented in Connector component and ConnectorBundle and we kept the old system in BaseConnectorBundle (deprecated for imports, still in use for exports).

In 1.5, for performance reason, we re-worked the export writer part, we introduced new classes and services in Connector component and ConnectorBundle.

Old export writer classes and services are still in BaseConnectorBundle and are marked as deprecated.

The strategy is to be able to depreciate entirely the BaseConnectorBundle once we'll have re-worked remaining export parts (mainly reader and processor).

TODO:
 - re-work legacy readers and processors pieces in Connector component
 - depreciate legacy readers and processors pieces in BaseConnector bundle
