<?php

namespace Ecsso\InStockSort\Plugin;

class FulltextCollectionPlugin
{
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
                'e.entity_id = stock_status.product_id',
                ['stock_status']
            );
        }

        // Sort: In stock first (1), Out of stock last (0)
        $collection->getSelect()->order('stock_status.stock_status DESC');

        return null;
    }
}
