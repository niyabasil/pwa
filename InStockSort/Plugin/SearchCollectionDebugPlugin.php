<?php

declare(strict_types=1);

namespace Ecsso\InStockSort\Plugin;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * TEMPORARY DEBUG PLUGIN — identifies the active search chain for PWA Studio search.
 * Check: var/log/instocksort_debug.log after doing a search.
 */
class SearchCollectionDebugPlugin
{
    private string $logFile;

    public function __construct()
    {
        $this->logFile = BP . '/var/log/instocksort_debug.log';
    }

    /**
     * Fires if Amasty SortingProvider is in the search chain.
     */
    public function beforeExecute(
        \Amasty\ElasticSearch\Model\Search\GetRequestQuery\SortingProvider $subject,
        \Magento\Framework\Search\RequestInterface $request
    ) {
        $this->write('>>> [1] AMASTY SortingProvider::execute() — Amasty adapter IS in the chain.');
    }

    /**
     * Fires if Magento core Fulltext Collection is used.
     */
    public function beforeLoad(
        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection,
        $printQuery = false,
        $logQuery = false
    ) {
        $this->write('>>> [2] MAGENTO Fulltext\Collection::load() — Core ES handling search.');
        return [$printQuery, $logQuery];
    }

    /**
     * Fires if GraphQL ProductSearch DataProvider is building collection criteria.
     * This IS in the chain for PWA Studio search.
     */
    public function afterBuild(
        \Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ProductSearch\ProductCollectionSearchCriteriaBuilder $subject,
        SearchCriteriaInterface $result,
        SearchCriteriaInterface $searchCriteria
    ): SearchCriteriaInterface {
        $sortInfo = [];
        foreach ((array)$result->getSortOrders() as $sort) {
            $sortInfo[] = $sort->getField() . ':' . $sort->getDirection();
        }

        $this->write(
            '>>> [3] ProductCollectionSearchCriteriaBuilder::build() fired.' . PHP_EOL .
            '    Current sort orders: [' . implode(', ', $sortInfo) . ']' . PHP_EOL .
            '    Page: ' . $result->getCurrentPage() . ', Size: ' . $result->getPageSize()
        );

        return $result;
    }

    private function write(string $message): void
    {
        file_put_contents(
            $this->logFile,
            '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }
}
