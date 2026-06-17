<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 *
 * Override of ExtractDataFromCategoryTree to sort children alphabetically by name
 * instead of the default position sort.
 */

namespace Tigren\Pwa\Override\Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider;

class ExtractDataFromCategoryTree extends \Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ExtractDataFromCategoryTree
{
    /**
     * Sort children alphabetically by name instead of by position.
     *
     * @param array $tree
     * @return array
     */
    protected function sortTree(array $tree): array
    {
        foreach ($tree as &$node) {
            if (!empty($node['children'])) {
                uasort($node['children'], function ($element1, $element2) {
                    return strcasecmp($element1['name'] ?? '', $element2['name'] ?? '');
                });
                $node['children'] = $this->sortTree($node['children']);
            }
        }
        return $tree;
    }
}
