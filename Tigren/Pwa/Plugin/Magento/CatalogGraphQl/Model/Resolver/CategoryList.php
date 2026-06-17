<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CatalogGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CategoryList
{
    public function afterResolve(
        \Magento\CatalogGraphQl\Model\Resolver\CategoryList $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!is_array($result)) {
            return $result;
        }

        return $this->sortChildrenAlphabetically($result);
    }

    private function sortChildrenAlphabetically(array $categories): array
    {
        foreach ($categories as &$category) {
            if (!empty($category['children'])) {
                usort($category['children'], function ($a, $b) {
                    return strcasecmp($a['name'] ?? '', $b['name'] ?? '');
                });
                $category['children'] = $this->sortChildrenAlphabetically($category['children']);
            }
        }
        return $categories;
    }
}
