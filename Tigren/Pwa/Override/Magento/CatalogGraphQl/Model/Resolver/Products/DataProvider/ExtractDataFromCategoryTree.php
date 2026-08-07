<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 *
 * Override ExtractDataFromCategoryTree to sort category children alphabetically.
 * sortTree() in parent is private — we override buildTree() (public) and re-sort after.
 */

namespace Tigren\Pwa\Override\Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider;

use Magento\Catalog\Model\ResourceModel\Category\Collection;

class ExtractDataFromCategoryTree extends \Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ExtractDataFromCategoryTree
{
    /**
     * Override buildTree to re-sort children alphabetically after parent builds the tree.
     * Parent's sortTree() is private so it still sorts by position internally,
     * but we re-sort the final result here by name.
     */
    public function buildTree(Collection $collection, array $topLevelCategoryIds): array
    {
        // Temporary debug log — remove after confirming override is called
        file_put_contents(
            BP . '/var/log/tigren_alphasort.log',
            date('[Y-m-d H:i:s]') . ' buildTree() override called. IDs: ' . implode(',', $topLevelCategoryIds) . PHP_EOL,
            FILE_APPEND
        );

        $result = parent::buildTree($collection, $topLevelCategoryIds);

        $result = $this->sortChildrenAlphabetically($result);

        file_put_contents(
            BP . '/var/log/tigren_alphasort.log',
            date('[Y-m-d H:i:s]') . ' sorting done. Top-level count: ' . count($result) . PHP_EOL,
            FILE_APPEND
        );

        return $result;
    }

    /**
     * Recursively sort children arrays by name (A-Z, case-insensitive).
     */
    private function sortChildrenAlphabetically(array $tree): array
    {
        foreach ($tree as &$node) {
            if (!empty($node['children'])) {
                uasort($node['children'], function ($a, $b) {
                    return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
                });
                $node['children'] = $this->sortChildrenAlphabetically($node['children']);
            }
        }
        return $tree;
    }
}
