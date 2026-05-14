<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\Directory\Model;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Currency
 * @package Tigren\Pwa\Plugin\Magento\Directory\Model
 */
class Currency
{
    /**
     * @param \Magento\Directory\Model\Currency $subject
     * @param $price
     * @param array $options
     * @return array
     */
    public function beforeFormatTxt(\Magento\Directory\Model\Currency $subject, $price, $options = [])
    {
        if (array_key_exists('precision', $options) && !is_numeric($options['precision'])) {
            $options['precision'] = PriceCurrencyInterface::DEFAULT_PRECISION;
        }

        return [$price, $options];
    }
}
