<?php

declare(strict_types=1);

namespace Ecsso\InStockSort\Plugin;

use Magento\Framework\Search\RequestInterface;

/**
 * TEMPORARY DEBUG PLUGIN — remove after identifying which search layer is active.
 *
 * Registered against two classes in di.xml:
 *   1. Amasty\ElasticSearch\Model\Search\GetRequestQuery\SortingProvider
 *   2. Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
 *
 * Check var/log/instocksort_debug.log after doing a search in PWA Studio.
 */
class SearchCollectionDebugPlugin
{
    private string $logFile;

    public function __construct()
    {
        $this->logFile = BP . '/var/log/instocksort_debug.log';
    }

    /**
     * Fires if Amasty ElasticSearch SortingProvider is the active search engine.
     */
    public function beforeExecute(
        \Amasty\ElasticSearch\Model\Search\GetRequestQuery\SortingProvider $subject,
        RequestInterface $request
    ) {
        $this->write('>>> AMASTY SortingProvider::execute() called — Amasty ElasticSearch is handling search.');
    }

    /**
     * Fires if Magento core Fulltext Collection is the active search engine.
     */
    public function beforeLoad(
        \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection,
        $printQuery = false,
        $logQuery = false
    ) {
        $this->write('>>> MAGENTO CORE Fulltext\Collection::load() called — Core Elasticsearch is handling search.');
        $this->write('    Actual class: ' . get_class($collection));
        return [$printQuery, $logQuery];
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
