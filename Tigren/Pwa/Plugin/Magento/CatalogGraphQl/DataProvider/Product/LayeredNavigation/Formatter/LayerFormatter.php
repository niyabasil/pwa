<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Formatter;

/**
 * Class LayerFormatter
 * @package Tigren\Pwa\Plugin\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Formatter
 */
class LayerFormatter
{
    /**
     * Format layer item data
     *
     * @param string $label
     * @param string|int $value
     * @param string|int $count
     * @return array
     */
    public function aroundBuildItem(
        \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\Formatter\LayerFormatter $subject,
        \Closure $proceed,
        $label,
        $value,
        $count,
        $isBooleanFilter = false
    ): array {
        return [
            'label' => $isBooleanFilter ? $label ? __('Yes') : __('No') : $label,
            'value' => $value,
            'count' => $count,
            'is_boolean_filter' => $isBooleanFilter
        ];
    }
}
