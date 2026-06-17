<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 *
 * Override ExtractDataFromCategoryTree to sort category children alphabetically.
 * NOTE: sortTree() in the parent is private so cannot be overridden — we override
 * buildTree() instead and replace the sort logic inline.
 */

namespace Tigren\Pwa\Override\Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider;

use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Psr\Log\LoggerInterface;

class ExtractDataFromCategoryTree extends \Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ExtractDataFromCategoryTree
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\CategoryTree\Wrapper\NodeWrapperFactory $nodeWrapperFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($nodeWrapperFactory);
        $this->logger = $logger;
    }

    /**
     * Override buildTree to sort children alphabetically by name.
     * The parent's sortTree() is private so we re-implement sorting here.
     */
    public function buildTree(Collection $collection, array $topLevelCategoryIds): array
    {
        $this->logger->debug('[Tigren AlphaSort] buildTree() override called. topLevelCategoryIds: ' . implode(',', $topLevelCategoryIds));

        $result = parent::buildTree($collection, $topLevelCategoryIds);

        $this->logger->debug('[Tigren AlphaSort] buildTree() result keys: ' . implode(',', array_keys($result)));

        $result = $this->sortAlphabetically($result);

        return $result;
    }

    /**
     * Recursively sort children arrays alphabetically by name.
     */
    private function sortAlphabetically(array $tree): array
    {
        foreach ($tree as &$node) {
            if (!empty($node['children'])) {
                $this->logger->debug('[Tigren AlphaSort] Sorting children of category: ' . ($node['name'] ?? 'unknown'));
                uasort($node['children'], function ($a, $b) {
                    return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
                });
                $node['children'] = $this->sortAlphabetically($node['children']);
            }
        }
        return $tree;
    }
}
