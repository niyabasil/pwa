<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Override\Magento\CatalogInventory\Model\Quote\Item\QuantityValidator;

/**
 * Class QuoteItemQtyList
 * @package Tigren\Pwa\Override\Magento\CatalogInventory\Model\Quote\Item\QuantityValidator
 */
class QuoteItemQtyList extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList
{
    /**
     * @return void
     */
    public function resetItemQtyList() {
        $this->_checkedQuoteItems = [];
    }
}
