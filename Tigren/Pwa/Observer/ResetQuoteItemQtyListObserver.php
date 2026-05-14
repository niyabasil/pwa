<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Observer;

use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ResetQuoteItemQtyListObserver
 * @package Tigren\Pwa\Observer
 */
class ResetQuoteItemQtyListObserver implements ObserverInterface
{
    /**
     * @var QuoteItemQtyList
     */
    protected $quoteItemQtyList;

    /**
     * @param QuoteItemQtyList $quoteItemQtyList
     */
    public function __construct(
        QuoteItemQtyList $quoteItemQtyList
    ) {
        $this->quoteItemQtyList = $quoteItemQtyList;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->quoteItemQtyList->resetItemQtyList();
    }
}
