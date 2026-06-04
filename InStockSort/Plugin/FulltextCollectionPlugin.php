<?php

namespace Ecsso\InStockSort\Plugin;

class FulltextCollectionPlugin
{
    /**
     * Join stock_status table so each product item has stock_status data available.
     * The ORDER BY here is overridden by Elasticsearch's FIELD() ordering — that's OK,
     * sorting is handled by ElasticsearchQueryPlugin (global) and afterGetItems (within-page).
     */
    public function beforeLoad(
        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection
    ) {
        if ($collection->isLoaded()) {
            return;
        }

        $select = $collection->getSelect();
        $from = $select->getPart(\Zend_Db_Select::FROM);

        if (!isset($from['stock_status'])) {
            $collection->getSelect()->joinLeft(
                ['stock_status' => $collection->getTable('cataloginventory_stock_status')],
                'e.entity_id = stock_status.product_id AND stock_status.stock_id = 1',
                ['stock_status']
            );
        }

        return null;
    }

    /**
     * Re-sort items after load: in-stock first, out-of-stock last.
     * Handles within-page ordering as a safety net alongside the ES-level sort.
     */
    public function afterGetItems(
        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection,
        array $items
    ): array {
        if (empty($items)) {
            return $items;
        }

        $inStock = [];
        $outOfStock = [];

        foreach ($items as $id => $item) {
            if ((int)$item->getData('stock_status') === 1) {
                $inStock[$id] = $item;
            } else {
                $outOfStock[$id] = $item;
            }
        }

        return array_merge($inStock, $outOfStock);
    }
}
