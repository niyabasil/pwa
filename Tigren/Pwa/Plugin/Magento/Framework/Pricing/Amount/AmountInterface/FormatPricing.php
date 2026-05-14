<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\Framework\Pricing\Amount\AmountInterface;

/**
 * Class FormatPricing
 * @package Magento\Framework\Pricing\Amount
 */
class FormatPricing
{
    /**
     * @param \Magento\Framework\Pricing\Amount\AmountInterface $AmountInterface
     * @param $result
     * @return float
     */
    public function afterGetValue(
        \Magento\Framework\Pricing\Amount\AmountInterface $AmountInterface,
        $result
    ) {
        if ($result) {
            return (float)number_format($result, 2, '.', '');
        }
    }
}
