<?php

declare(strict_types=1);

namespace Ecsso\InStockSort\Plugin;

use Magento\Framework\Search\RequestInterface;

/**
 * Prepend is_out_of_stock sort to Amasty ElasticSearch query.
 *
 * Amasty indexes is_out_of_stock per product (stores is_salable value):
 *   1 = in stock (salable),  0 = out of stock
 * Sorting DESC puts in-stock products first globally, so pagination
 * produces all in-stock pages first and out-of-stock on the last page.
 */
class AmastySearchSortingPlugin
{
    public function afterExecute(
        \Amasty\ElasticSearch\Model\Search\GetRequestQuery\SortingProvider $subject,
        array $sortings,
        RequestInterface $request
    ): array {
        array_unshift($sortings, [
            'is_out_of_stock' => ['order' => 'desc']
        ]);

        return $sortings;
    }
}
