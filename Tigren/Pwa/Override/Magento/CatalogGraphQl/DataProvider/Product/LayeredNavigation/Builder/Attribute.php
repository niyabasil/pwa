<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Override\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder;

use Generator;
use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\AttributeOptionProvider;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\AggregationValueInterface;
use Magento\Framework\Api\Search\BucketInterface;
use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Formatter\LayerFormatter;
use Zend_Db_Statement_Exception;
use function array_map;
use function array_merge;
use function count;
use function in_array;
use function preg_replace;

/**
 * Class Attribute
 */
class Attribute extends \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder\Attribute
{
    /**
     * @var string
     * @see \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Category::CATEGORY_BUCKET
     */
    private const PRICE_BUCKET = 'price_bucket';

    /**
     * @var string
     * @see \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder\Price::PRICE_BUCKET
     */
    private const CATEGORY_BUCKET = 'category_bucket';

    /**
     * @var AttributeOptionProvider
     */
    private $attributeOptionProvider;

    /**
     * @var LayerFormatter
     */
    private $layerFormatter;

    /**
     * @var array
     */
    private $bucketNameFilter = [
        self::PRICE_BUCKET,
        self::CATEGORY_BUCKET
    ];

    /**
     * @param AttributeOptionProvider $attributeOptionProvider
     * @param LayerFormatter $layerFormatter
     * @param array $bucketNameFilter
     */
    public function __construct(
        AttributeOptionProvider $attributeOptionProvider,
        LayerFormatter $layerFormatter,
        $bucketNameFilter = []
    ) {
        parent::__construct($attributeOptionProvider, $layerFormatter, $bucketNameFilter);
        $this->attributeOptionProvider = $attributeOptionProvider;
        $this->layerFormatter = $layerFormatter;
        $this->bucketNameFilter = array_merge($this->bucketNameFilter, $bucketNameFilter);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws Zend_Db_Statement_Exception
     */
    public function build(AggregationInterface $aggregation, ?int $storeId): array
    {
        $attributeOptions = $this->getAttributeOptions($aggregation, $storeId);

        // build layer per attribute
        $result = [];
        foreach ($this->getAttributeBuckets($aggregation) as $bucket) {
            $bucketName = $bucket->getName();
            $attributeCode = preg_replace('~_bucket$~', '', $bucketName);
            $attribute = $attributeOptions[$attributeCode] ?? [];
            $options = $attribute['options'] ?? [];

            $result[$bucketName] = $this->layerFormatter->buildLayer(
                $attribute['attribute_label'] ?? $bucketName,
                count($bucket->getValues()),
                $attribute['attribute_code'] ?? $bucketName
            );
            $result[$bucketName]['options'] = [];
            $newOptions = [];
            foreach ($bucket->getValues() as $value) {
                $metrics = $value->getMetrics();
                $newOptions[$metrics['value']] = $metrics['count'];
                if (!isset($options[$metrics['value']])) {
                    $options[$metrics['value']] = $metrics['value'];
                }
            }

            if ($options) {
                foreach ($options as $key => $value) {
                    if (!isset($newOptions[$key])) {
                        continue;
                    }
                    $result[$bucketName]['options'][] = $this->layerFormatter->buildItem(
                        $value ?? $key,
                        $key,
                        $newOptions[$key],
                        $this->isBooleanFilter($bucket)
                    );
                }
            } else {
                foreach ($bucket->getValues() as $value) {
                    $metrics = $value->getMetrics();

                    $result[$bucketName]['options'][] = $this->layerFormatter->buildItem(
                        $attribute['options'][$metrics['value']] ?? $metrics['value'],
                        $metrics['value'],
                        $metrics['count']
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @param $bucket
     * @return bool
     */
    public function isBooleanFilter($bucket)
    {
        if ($bucket->getValues() && count($bucket->getValues()) <= 2) {
            foreach ($bucket->getValues() as $value) {
                $metrics = $value->getMetrics();
                if ($metrics && isset($metrics['value'])) {
                    $valueMetrics = $metrics['value'];
                    if ($valueMetrics !== 0 && $valueMetrics !== 1) {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get attribute buckets excluding specified bucket names
     *
     * @param AggregationInterface $aggregation
     * @return Generator|BucketInterface[]
     */
    private function getAttributeBuckets(AggregationInterface $aggregation)
    {
        foreach ($aggregation->getBuckets() as $bucket) {
            if (in_array($bucket->getName(), $this->bucketNameFilter, true)) {
                continue;
            }
            if ($this->isBucketEmpty($bucket)) {
                continue;
            }
            yield $bucket;
        }
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
     * Get list of attributes with options
     *
     * @param AggregationInterface $aggregation
     * @param int|null $storeId
     * @return array
     * @throws Zend_Db_Statement_Exception
     */
    private function getAttributeOptions(AggregationInterface $aggregation, ?int $storeId): array
    {
        $attributeOptionIds = [];
        $attributes = [];
        foreach ($this->getAttributeBuckets($aggregation) as $bucket) {
            $attributes[] = preg_replace('~_bucket$~', '', $bucket->getName());
            $attributeOptionIds[] = array_map(
                function (AggregationValueInterface $value) {
                    return $value->getValue();
                },
                $bucket->getValues()
            );
        }

        if (!$attributeOptionIds) {
            return [];
        }

        return $this->attributeOptionProvider->getOptions(
            array_merge([], ...$attributeOptionIds),
            $storeId,
            $attributes
        );
    }
}
