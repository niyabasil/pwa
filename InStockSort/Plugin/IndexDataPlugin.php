<?php

namespace Ecsso\InStockSort\Plugin;

use Magento\Framework\App\ResourceConnection;

/**
 * Adds stock_status (0/1) to each product's Elasticsearch document during indexing.
 * After running catalogsearch_fulltext reindex, the field becomes sortable in ES.
 */
class IndexDataPlugin
{
    private ResourceConnection $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function afterGetProductAttributes(
        \Magento\CatalogSearch\Model\Indexer\Fulltext\Action\DataProvider $subject,
        array $result,
        $storeId,
        $productIds,
        array $attributeData
    ): array {
        if (empty($result)) {
            return $result;
        }

        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('cataloginventory_stock_status');

        $select = $connection->select()
            ->from($table, ['product_id', 'stock_status'])
            ->where('product_id IN (?)', array_keys($result))
            ->where('stock_id = 1');

        $stockStatuses = $connection->fetchPairs($select);

        foreach ($result as $productId => &$productData) {
            $productData['stock_status'] = (int)($stockStatuses[$productId] ?? 0);
        }

        return $result;
    }
}
