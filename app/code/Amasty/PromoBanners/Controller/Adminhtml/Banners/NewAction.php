<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;

class NewAction extends \Magento\Backend\App\Action
{
    public function execute()
    {
        $this->_forward('edit');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}
