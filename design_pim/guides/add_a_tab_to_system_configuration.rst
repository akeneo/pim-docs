How to add a new tab in System / Configuration
==============================================

To add or override a tab in the System Configuration page, you can use our form extensions system. The first step is to create a form extension in your custom bundle.

.. code-block:: javascript
    :linenos:

    'use strict';
    /*
     * /src/Acme/Bundle/BlogBundle/Resources/public/js/configuration/tab.js
     */
    define(['pim/form'],
        function (BaseForm) {
            return BaseForm.extend({
                configure: function () {
                    this.trigger('tab:register', {
                        code: this.code,
                        isVisible: this.isVisible.bind(this),
                        label: 'My tab'
                    });

                    return BaseForm.prototype.configure.apply(this, arguments);
                },
                render: function () {
                    this.$el.html('Hello world');

                    return this;
                },
                isVisible: function () {
                    return true;
                }
            });
        }
    );

Once you create your new tab extension, you have register it in the requirejs.yml file for your bundle:

.. code-block:: yaml
    :linenos:

    # /src/Acme/Bundle/BlogBundle/Resources/config/requirejs.yml

    config:
        paths:
            pim/configuration/tab: acme/js/configuration/tab


Now that your file is registered, you can add the extension to the Configuration page:

.. code-block:: yaml
    :linenos:

    extensions:
        oro-system-config-tab-acme:
            module: pim/configuration/tab
            parent: oro-system-config-tabs
            targetZone: container
            position: 110

To see your new tab in the Configuration page your need to clear the PIM cache and run webpack:

.. code-block:: bash

    rm -rf ./var/cache/*
    yarn run webpack
