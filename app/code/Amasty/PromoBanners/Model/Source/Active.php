<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Active implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            '0' => __('Inactive'),
            '1' => __('Active'),
        ];
    }
}
