<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\PwaPerformance\Plugin\Magento\CatalogGraphQl\Model\Resolver\Category;

/**
 * Class CategoriesIdentity
 * @package Tigren\PwaPerformance\Plugin\Magento\CatalogGraphQl\Model\Resolver\Category
 */
class CategoriesIdentity
{
    /** @var string */
    private $cacheTag = 'pwa_category_list';

    /**
     * @param \Magento\CatalogGraphQl\Model\Resolver\Category\CategoriesIdentity $subject
     * @param $result
     * @param array $resolvedData
     * @return array
     */
    public function afterGetIdentities(
        \Magento\CatalogGraphQl\Model\Resolver\Category\CategoriesIdentity $subject,
        $result,
        array $resolvedData
    ): array {
        if (!empty($resolvedData[0]['id'])) {
            return [sprintf('%s_%s', $this->cacheTag, $resolvedData[0]['id'])];
        }
        return [$this->cacheTag];
    }
}
