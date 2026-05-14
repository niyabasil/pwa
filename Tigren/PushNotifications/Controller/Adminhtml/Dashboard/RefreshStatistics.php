<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Dashboard;

use Tigren\PushNotifications\Controller\Adminhtml\Dashboard;
use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Psr\Log\LoggerInterface;

/**
 * Class RefreshStatistics
 * @package Tigren\PushNotifications\Controller\Adminhtml\Dashboard
 */
class RefreshStatistics extends Dashboard
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        try {
            $this->messageManager->addSuccessMessage(__('We updated lifetime statistic.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('We can\'t refresh lifetime statistics.'));
            $this->logger->critical($e);
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*');
    }
}
