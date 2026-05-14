<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Controller\Adminhtml\Campaign;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Tigren\PushNotifications\Model\CampaignFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Edit
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class Edit extends Campaign
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
     * Edit action
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = $this->repository->getById($id);
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('This campaign no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        }

        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Tigren_PushNotifications::campaign');
        $resultPage->addBreadcrumb(__('Campaign'), __('Campaign'));
        $resultPage->getConfig()->getTitle()->prepend(
            isset($model) && $model->getId() ? $model->getName() : __('New Campaign')
        );

        return $resultPage;
    }
}
