// Resources/public/js/my-custom-choices-list.js

'use strict';

define([
    'jquery',
    'underscore',
    'oro/translator',
    'pim/fetcher-registry',
    'pim/job/common/edit/field/select'
], function (
    $,
    _,
    __,
    FetcherRegistry,
    SelectField
) {
    return SelectField.extend({
        /**
        * {@inherit}
        */
        configure: function () {
            return $.when(
                FetcherRegistry.getFetcher('%THE_NAME_OF_YOUR_FETCHER%').fetchAll(),
                SelectField.prototype.configure.apply(this, arguments)
            ).then(function (myCustomChoicesList) {
                if (_.isEmpty(myCustomChoicesList)) {
                    this.config.readOnly = true;
                    this.config.options = {'NO OPTION': __('%your_translation_key%')};
                } else {
                    this.config.options = myCustomChoicesList;
                }
            }.bind(this));
        }
    });
});