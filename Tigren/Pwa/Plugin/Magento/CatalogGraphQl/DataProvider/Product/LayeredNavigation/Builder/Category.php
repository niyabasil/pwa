<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder;

use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\GraphQl\Query\Uid;

/**
 * Class Category
 * @package Tigren\Pwa\Plugin\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder
 */
class Category
{
    /** @var Uid */
    private $uidEncoder;

    /**
     * @param Uid $uidEncoder
     */
    public function __construct(
        Uid $uidEncoder
    ) {
        $this->uidEncoder = $uidEncoder;
    }

    /**
     * @param \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder\Category $subject
     * @param $results
     * @param AggregationInterface $aggregation
     * @param int|null $storeId
     * @return array
     */
    public function afterBuild(
        \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Builder\Category $subject,
        $results,
        AggregationInterface $aggregation,
        ?int $storeId
    ): array {
        foreach ($results as &$result) {
            if (isset($result['options'])) {
                foreach ($result['options'] as &$option) {
                    if (isset($option['value']) && is_numeric($option['value'])) {
                        $option['value'] = $this->uidEncoder->encode((string)$option['value']);
                    }
                }
            }
        }

        return $results;
    }
}
