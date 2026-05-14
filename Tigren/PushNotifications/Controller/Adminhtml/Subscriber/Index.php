<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Subscriber;

use Tigren\PushNotifications\Controller\Adminhtml\Subscriber;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Tigren\PushNotifications\Controller\Adminhtml\Subscriber
 */
class Index extends Subscriber
{
    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Tigren_PushNotifications::subscriber_list');
        $resultPage->addBreadcrumb(__('Subscribers'), __('Subscribers'));
        $resultPage->addBreadcrumb(__('Manage Subscribers'), __('Manage Subscribers'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Subscribers'));

        return $resultPage;
    }
}
