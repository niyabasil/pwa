<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\Quote;

/**
 * Class Discounts
 * @package Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver
 */
class Discounts
{
    /**
     * @param \Magento\QuoteGraphQl\Model\Resolver\Discounts $subject
     * @param \Closure $proceed
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|mixed|null
     * @throws LocalizedException
     */
    public function aroundResolve(
        \Magento\QuoteGraphQl\Model\Resolver\Discounts $subject,
        \Closure $proceed,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        $quote = $value['model'];
        if ($quote->isVirtual()) {
            return $this->getDiscountValues($quote);
        }

        return $proceed(
            $field,
            $context,
            $info,
            $value,
            $args
        );
    }

    /**
     * @param Quote $quote
     * @return array|null
     */
    private function getDiscountValues(Quote $quote)
    {
        $discountValues = [];
        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();
        $totalDiscounts = $address->getExtensionAttributes()->getDiscounts();
        if ($totalDiscounts && is_array($totalDiscounts)) {
            foreach ($totalDiscounts as $value) {
                $discount = [];
                $amount = [];
                $discount['label'] = $value->getRuleLabel() ?: __('Discount');
                /* @var \Magento\SalesRule\Api\Data\DiscountDataInterface $discountData */
                $discountData = $value->getDiscountData();
                $amount['value'] = $discountData->getAmount();
                $amount['currency'] = $quote->getQuoteCurrencyCode();
                $discount['amount'] = $amount;
                $discountValues[] = $discount;
            }
            
            return $discountValues;
        }
        
        return null;
    }
}
