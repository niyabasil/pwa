<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Ui\Component\Listing\Column;

/**
 * Store Grid Filter
 */
class Stores extends \Magento\Store\Ui\Component\Listing\Column\Store
{
    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        if (isset($item[$this->storeKey])) {
            if (!$item[$this->storeKey]) {
                $item[$this->storeKey] = [0];
            }

            if (is_string($item[$this->storeKey])) {
                $item[$this->storeKey] = explode(',', $item[$this->storeKey]);
            }
        }

        return parent::prepareItem($item);
    }
}
