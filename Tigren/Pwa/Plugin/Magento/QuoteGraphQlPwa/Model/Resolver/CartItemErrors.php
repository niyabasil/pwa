<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQlPwa\Model\Resolver;

use Magento\CatalogInventory\Helper\Data;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class CartItemErrors
 * @package Tigren\Pwa\Plugin\Magento\QuoteGraphQlPwa\Model\Resolver
 */
class CartItemErrors
{
    /**
     * @param \Magento\QuoteGraphQlPwa\Model\Resolver\CartItemErrors $subject
     * @param $results
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed
     */
    public function afterResolve(
        \Magento\QuoteGraphQlPwa\Model\Resolver\CartItemErrors $subject,
        $results,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (is_array($results)) {
            foreach ($results as &$error) {
                if ($error['code'] === 'ITEM_QTY' && !$error['message']) {
                    $error['message'] = __('This product is out of stock.');
                }
            }
        }

        return $results;
    }
}
