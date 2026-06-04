<?php

namespace Ecsso\InStockSort\Plugin;

class ProductSearchPlugin
{
    public function beforeGetList(
        \Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ProductSearch $subject,
        $searchCriteria,
        $fields,
        $context
    ) {

        // Add sorting: in-stock first
        $sortOrders = $searchCriteria->getSortOrders() ?: [];

        $sortOrders[] = new \Magento\Framework\Api\SortOrder([
            'field' => 'is_salable',
            'direction' => 'DESC'
        ]);

        $searchCriteria->setSortOrders($sortOrders);

        return [$searchCriteria, $fields, $context];
    }
}