<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Override\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder;

use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\LayerBuilderInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\BucketInterface;
use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Formatter\LayerFormatter;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Price
 * @package Tigren\Pwa\Override\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder
 */
class Price implements LayerBuilderInterface
{
    /**
     * @var string
     */
    private const PRICE_BUCKET = 'price_bucket';

    /**
     * @var LayerFormatter
     */
    private $layerFormatter;

    /**
     * @var array
     */
    private static $bucketMap = [
        self::PRICE_BUCKET => [
            'request_name' => 'price',
            'label' => 'Price'
        ],
    ];

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param LayerFormatter $layerFormatter
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        LayerFormatter $layerFormatter,
        ResourceConnection $resourceConnection
    ) {
        $this->layerFormatter = $layerFormatter;
        $this->resourceConnection = $resourceConnection;

    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build(AggregationInterface $aggregation, ?int $storeId): array
    {
        $bucket = $aggregation->getBucket(self::PRICE_BUCKET);
        if ($this->isBucketEmpty($bucket)) {
            return [];
        }
        // Localize value of the price attribute
        $attributeData = $this->getAttributeData('price', $storeId);
        $attributeLabel = $attributeData['attribute_store_label'] ?? $attributeData['frontend_label'] ?? self::$bucketMap[self::PRICE_BUCKET]['label'];

        $result = $this->layerFormatter->buildLayer(
            $attributeLabel,
            \count($bucket->getValues()),
            self::$bucketMap[self::PRICE_BUCKET]['request_name']
        );

        foreach ($bucket->getValues() as $value) {
            $metrics = $value->getMetrics();
            $result['options'][] = $this->layerFormatter->buildItem(
                \str_replace('_', '-', $metrics['value']),
                $metrics['value'],
                $metrics['count']
            );
        }

        return [self::PRICE_BUCKET => $result];
    }

    /**
     * Check that bucket contains data
     *
     * @param BucketInterface|null $bucket
     * @return bool
     */
    private function isBucketEmpty(?BucketInterface $bucket): bool
    {
        return null === $bucket || !$bucket->getValues();
    }

    /**
     * Get root category for specified store id
     *
     * @param int $attributeCode
     * @param int $storeId
     */
    public function getAttributeData($attributeCode, $storeId)
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from(
                ['attribute' => $this->resourceConnection->getTableName('eav_attribute')]
            )
            ->joinLeft(
                ['attribute_label' => $this->resourceConnection->getTableName('eav_attribute_label')],
                "attribute.attribute_id = attribute_label.attribute_id AND attribute_label.store_id = {$storeId}",
                [
                    'attribute_store_label' => 'attribute_label.value',
                ]
            )
            ->where('attribute.attribute_code = ?', $attributeCode);

        return $connection->fetchRow($select);
    }
}
