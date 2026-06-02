<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Block\Adminhtml\Banners\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ampromobanners_banners_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Banner'));
    }
}
