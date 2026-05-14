<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\Review\Model\ResourceModel\Review;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Collection
 * @package Tigren\Pwa\Plugin\Magento\Review\Model\ResourceModel\Review
 */
class Collection
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $reviewCollection
     * @param $printQuery
     * @param $logQuery
     * @return array|false[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundAddStoreFilter(
        \Magento\Review\Model\ResourceModel\Review\Collection $reviewCollection,
        \Closure $proceed,
        $storeId
    ) {
        $storeFlag = 'has_store_filter';
        if (!$reviewCollection->hasFlag($storeFlag)) {
            $reviewCollection->setFlag($storeFlag, true);
            return $proceed($storeId);
        }

        return $reviewCollection;
    }

    /**
     * @param \Magento\Review\Model\ResourceModel\Review\Collection $reviewCollection
     * @param $printQuery
     * @param $logQuery
     * @return array|false[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeLoad(
        \Magento\Review\Model\ResourceModel\Review\Collection $reviewCollection,
        $printQuery = false,
        $logQuery = false
    ) {
        $storeId = $this->_storeManager->getStore()->getId();
        $reviewCollection->addStoreFilter($storeId);

        return [$printQuery, $logQuery];
    }
}
