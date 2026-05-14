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
use Magento\Quote\Model\Quote\Item;

/**
 * Class CartItems
 * Bypass cart item error message that caused graphql error
 * @package Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver
 */
class CartItemPrices
{
    /**
     * @param \Magento\QuoteGraphQl\Model\Resolver\CartItemPrices $subject
     * @param array $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|Value
     */
    public function afterResolve(
        \Magento\QuoteGraphQl\Model\Resolver\CartItemPrices $subject,
        array $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        /** @var Item $cartItem */
        $cartItem = $value['model'];
        $currencyCode = $cartItem->getQuote()->getQuoteCurrencyCode();

        $price = $cartItem->getPrice();

        $rowTotal = $cartItem->getRowTotal() > 0.0000 ? $cartItem->getRowTotal() : $price * $cartItem->getQty();
        $rowTotalInclTax = $cartItem->getRowTotalInclTax() > 0.0000 ? $cartItem->getRowTotalInclTax() : $rowTotal;

        return \array_merge($result, [
            'price_including_tax' => [
                'currency' => $currencyCode,
                'value' => (float) $cartItem->getPriceInclTax(),
            ],
            'row_total_including_tax' => [
                'currency' => $currencyCode,
                'value' => $rowTotalInclTax,
            ]
        ]);
    }
}
