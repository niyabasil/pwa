/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define(["jquery", "Tigren_PushNotifications/js/push_notification"], function(
    $,
    PushNotification
) {
    "use strict";

    return PushNotification.extend({
        /**
         * Initialize actions and adapter.
         *
         * @param {Object} config
         * @param {Element} elem
         * @returns {Object}
         */
        initialize: function(config, elem) {
            return this._super().initAdapter(elem);
        },

        /**
         *
         * @return {exports}
         */
        sendTestNotification: function() {
            this.sendTest(this.urlAction, this.campaignId);

            return this;
        },

        /**
         * Attach callback handler on button.
         *
         * @param {Element} elem
         */
        initAdapter: function(elem) {
            var self = this;

            $(elem).on("click", function() {
                self.sendTestNotification();
            });
        }
    });
});
