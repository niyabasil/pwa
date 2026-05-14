<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Override\Magento\CatalogGraphQl\Model\Resolver;

use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\LayerBuilder;
use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder\Aggregations\Category;
use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Aggregations
 * @package Tigren\Pwa\Override\Magento\CatalogGraphQl\Model\Resolver
 */
class Aggregations extends \Magento\CatalogGraphQl\Model\Resolver\Aggregations
{
    /**
     * @var \Magento\CatalogGraphQl\Model\Resolver\Layer\DataProvider\Filters
     */
    private $filtersDataProvider;

    /**
     * @var LayerBuilder
     */
    private $layerBuilder;

    /**
     * @var PriceCurrency|mixed
     */
    private $priceCurrency;

    /**
     * @var Category\IncludeDirectChildrenOnly|mixed
     */
    private $includeDirectChildrenOnly;

    /**
     * @param \Magento\CatalogGraphQl\Model\Resolver\Layer\DataProvider\Filters $filtersDataProvider
     * @param LayerBuilder $layerBuilder
     * @param PriceCurrency|null $priceCurrency
     * @param Category\IncludeDirectChildrenOnly|null $includeDirectChildrenOnly
     */
    public function __construct(
        \Magento\CatalogGraphQl\Model\Resolver\Layer\DataProvider\Filters $filtersDataProvider,
        LayerBuilder $layerBuilder,
        PriceCurrency $priceCurrency = null,
        Category\IncludeDirectChildrenOnly $includeDirectChildrenOnly = null
    ) {
        $this->filtersDataProvider = $filtersDataProvider;
        $this->layerBuilder = $layerBuilder;
        $this->priceCurrency = $priceCurrency ?: ObjectManager::getInstance()->get(PriceCurrency::class);
        $this->includeDirectChildrenOnly = $includeDirectChildrenOnly
            ?: ObjectManager::getInstance()->get(Category\IncludeDirectChildrenOnly::class);
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed|null
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['layer_type']) || !isset($value['search_result'])) {
            return null;
        }

        $aggregations = $value['search_result']->getSearchAggregation();

        if ($aggregations) {
            $categoryFilter = $value['categories'] ?? [];
            $includeDirectChildrenOnly = $args['filter']['category']['includeDirectChildrenOnly'] ?? false;
            if ($includeDirectChildrenOnly && !empty($categoryFilter)) {
                $this->includeDirectChildrenOnly->setFilter(['category' => $categoryFilter]);
            }
            /** @var StoreInterface $store */
            $store = $context->getExtensionAttributes()->getStore();
            $storeId = (int)$store->getId();
            $results = $this->layerBuilder->build($aggregations, $storeId);
            if (isset($results['price_bucket'])) {
                foreach ($results['price_bucket']['options'] as &$value) {
                    list($from, $to) = explode('-', $value['label']);
                    $newLabel = $this->priceCurrency->convertAndRound($from)
                        . '-'
                        . $this->priceCurrency->convertAndRound($to);
                    $value['label'] = $newLabel;
                }
            }
            return $results;
        } else {
            return [];
        }
    }
}
