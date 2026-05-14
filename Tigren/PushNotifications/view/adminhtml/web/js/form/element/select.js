/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

define([
    "jquery",
    "underscore",
    "uiRegistry",
    "Magento_Ui/js/form/element/select"
], function($, _, uiRegistry, select) {
    "use strict";

    return select.extend({
        setOptionDisable: function(option, item) {
            option.disabled = item.disabled;
        }
    });
});
