<?php

namespace Ecsso\InStockSort\Plugin;

use Magento\Framework\Search\RequestInterface;

/**
 * Prepend stock_status sort to the Elasticsearch query before execution.
 * This ensures Elasticsearch itself paginates with in-stock items first,
 * so page 1-N show in-stock and the last page shows out-of-stock globally.
 */
class ElasticsearchQueryPlugin
{
    public function afterBuild(
        \Magento\Elasticsearch\SearchAdapter\Query\Builder $subject,
        array $query,
        RequestInterface $request
    ): array {
        if (!isset($query['body']['sort'])) {
            $query['body']['sort'] = [];
        }

        // stock_status: 1 = in stock, 0 = out of stock — DESC puts in-stock first
        array_unshift($query['body']['sort'], ['stock_status' => ['order' => 'desc']]);

        return $query;
    }
}
