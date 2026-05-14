<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Subscriber;

use Tigren\PushNotifications\Controller\Adminhtml\Subscriber;
use Magento\Backend\App\Action\Context;
use Tigren\PushNotifications\Api\SubscriberRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Delete
 * @package Tigren\PushNotifications\Controller\Adminhtml\Subscriber
 */
class Delete extends Subscriber
{
    /**
     * @var SubscriberRepositoryInterface
     */
    private $repository;

    public function __construct(
        Context $context,
        SubscriberRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->repository = $repository;
    }

    /**
     * Delete action
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Subscriber is deleted.'));
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Subscriber ID is not found.'));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
