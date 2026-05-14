<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class CartItems
 * Bypass cart item error message that caused graphql error
 * @package Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver
 */
class CartItems
{
    /**
     * @param \Magento\QuoteGraphQl\Model\Resolver\CartItems $subject
     * @param $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|Value
     */
    public function afterResolve(
        \Magento\QuoteGraphQl\Model\Resolver\CartItems $subject,
        array $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (is_array($result)) {
            /** @var QuoteItem $cartItem */
            foreach ($result as $cartItemKey => &$cartItem) {
                if (!is_array($cartItem)) {
                    unset($result[$cartItemKey]);
                }
            }
        }

        return $result;
    }
}
