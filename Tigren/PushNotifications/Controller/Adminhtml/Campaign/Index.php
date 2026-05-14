<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Controller\Adminhtml\Campaign;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class Index extends Campaign
{
    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Tigren_PushNotifications::campaign_list');
        $resultPage->addBreadcrumb(__('Campaigns'), __('Campaigns'));
        $resultPage->addBreadcrumb(__('Manage Campaigns'), __('Manage Campaigns'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Campaigns'));

        return $resultPage;
    }
}
