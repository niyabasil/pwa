<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Controller\Adminhtml\Campaign;
use Tigren\PushNotifications\Model\CampaignRepository;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Class Delete
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class Delete extends Campaign
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    public function __construct(
        Action\Context $context,
        LoggerInterface $logger,
        CampaignRepository $campaignRepository
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->campaignRepository = $campaignRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->campaignRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the campaign.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
        }

        $this->_redirect('*/*/');
    }
}
