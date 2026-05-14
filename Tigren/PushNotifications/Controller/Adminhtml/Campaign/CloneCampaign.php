<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Controller\Adminhtml\Campaign;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Tigren\PushNotifications\Model\CampaignFactory;
use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class CloneCampaign
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class CloneCampaign extends Campaign
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    public function __construct(
        Context $context,
        CampaignRepositoryInterface $repository,
        CampaignFactory $campaignFactory
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->campaignFactory = $campaignFactory;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = $this->repository->getById($id);
                $campaign = $this->campaignFactory->create();
                $campaign->setName($model->getName());
                $campaign->setScheduled($model->getScheduled());
                $campaign->setMessageTitle($model->getMessageTitle());
                $campaign->setMessageBody($model->getMessageBody());
                $campaign->setLogoPath($model->getLogoPath());
                $campaign->setButtonNotificationUrl($model->getButtonNotificationUrl());
                $campaign->setButtonNotificationText($model->getButtonNotificationText());
                $campaign->setUtmParams($model->getUtmParams());
                $campaign = $this->repository->save($campaign);

                return $this->_redirect('*/*/edit', ['id' => $campaign->getId()]);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This Campaign no longer exists.'));
            } catch (CouldNotSaveException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}
