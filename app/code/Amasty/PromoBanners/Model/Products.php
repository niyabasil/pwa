<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Model;

use Magento\Framework\Model\AbstractModel;

class Products extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Amasty\PromoBanners\Model\ResourceModel\Products');
    }
}
