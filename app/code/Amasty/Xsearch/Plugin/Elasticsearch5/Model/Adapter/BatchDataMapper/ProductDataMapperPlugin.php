<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Search Base for Magento 2
 */

namespace Amasty\Xsearch\Plugin\Elasticsearch5\Model\Adapter\BatchDataMapper;

use Amasty\Xsearch\Model\Indexer\ElasticSearchStockStatusDataMapper;
use Amasty\Xsearch\Model\Indexer\ElasticSearchStockStatusStructureMapper;
use Magento\Elasticsearch\Model\Adapter\BatchDataMapper\ProductDataMapper;

class ProductDataMapperPlugin
{
    /**
     * @var ElasticSearchStockStatusDataMapper
     */
    private $mapper;

    public function __construct(ElasticSearchStockStatusDataMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Prepare index data for using in search engine metadata.
     *
     * Amasty_ShopBy have plugin for the same class and may change the result document
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ProductDataMapper $subject
     * @param array $documentData
     * @param array $documentDataInput
     * @param int|string $storeId
     * @param array $context
     * @return array
     */
    public function afterMap(
        $subject,
        array $documentData,
        array $documentDataInput,
        $storeId,
        $context = []
    ): array {
        $stockData = $this->mapper->map($documentData, $storeId, $context);
        $fieldName = ElasticSearchStockStatusStructureMapper::STOCK_STATUS;
        foreach ($documentData as $productId => $document) {
            if (isset($stockData[$productId])) {
                $documentData[$productId][$fieldName] = $stockData[$productId][$fieldName];
            }
        }

        return $documentData;
    }
}
