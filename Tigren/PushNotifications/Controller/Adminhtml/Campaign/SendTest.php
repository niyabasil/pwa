<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Controller\Adminhtml\Campaign;
use Tigren\PushNotifications\Controller\RegistryConstants;
use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\Processor\NotificationProcessor;
use Tigren\PushNotifications\Model\Processor\SubscriberProcessor;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class SendTest
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class SendTest extends Campaign
{
    /**
     * @var Request
     */
    private $restRequest;

    /**
     * @var NotificationProcessor
     */
    private $notificationProcessor;

    /**
     * @var SubscriberProcessor
     */
    private $subscriberProcessor;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Action\Context $context,
        Request $restRequest,
        NotificationProcessor $notificationProcessor,
        SubscriberProcessor $subscriberProcessor,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->restRequest = $restRequest;
        $this->notificationProcessor = $notificationProcessor;
        $this->subscriberProcessor = $subscriberProcessor;
        $this->registry = $registry;
    }

    /**
     * @return ResponseInterface|ResultInterface
     *
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $result = [];
        $this->registry->register(RegistryConstants::REGISTRY_TEST_NOTIFICATION_NAME, 1);

        try {
            if (isset($params['campaignId'])
                && isset($params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME])
            ) {
                $result = $this->notificationProcessor->processByToken(
                    (int)$params['campaignId'],
                    $params['userToken'],
                    true
                );

                $this->subscriberProcessor->process($params);
            }
        } catch (NotificationException $exception) {
            $result = [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }

        $this->registry->unregister(RegistryConstants::REGISTRY_TEST_NOTIFICATION_NAME);
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }

    /**
     * Check url keys. If non valid - redirect
     *
     * @return bool
     */
    public function _processUrlKeys()
    {
        $this->getRequest()->setParams($this->getParamsFromRequestContent());

        return parent::_processUrlKeys();
    }

    /**
     * @return array
     */
    private function getParamsFromRequestContent()
    {
        $params = [];
        parse_str((string)$this->restRequest->getContent(), $params);

        return $params;
    }
}
