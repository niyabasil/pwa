<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Block\Adminhtml;

class Banners extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_banners';
        $this->_headerText = __('Banners');
        $this->_blockGroup = 'Amasty_PromoBanners';
        $this->_addButtonLabel = 'Add Banner';
        parent::_construct();
    }
}
