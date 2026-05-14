<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Processor;

use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Api\SubscriberRepositoryInterface;
use Tigren\PushNotifications\Controller\RegistryConstants;
use Tigren\PushNotifications\Model\Builder\CustomerDataBuilder;
use Tigren\PushNotifications\Model\SubscriberFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SubscriberProcessor
 * @package Tigren\PushNotifications\Model\Processor
 */
class SubscriberProcessor
{
    /**
     * @var CustomerDataBuilder
     */
    private $customerDataBuilder;

    /**
     * @var SubscriberRepositoryInterface
     */
    private $subscriberRepository;

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        CustomerDataBuilder $customerDataBuilder,
        SubscriberRepositoryInterface $subscriberRepository,
        SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customerDataBuilder = $customerDataBuilder;
        $this->subscriberRepository = $subscriberRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     * @param array $params
     * @return SubscriberProcessor
     * @throws CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Tigren\PushNotifications\Exception\NotificationException
     */
    public function process(array $params)
    {
        if (isset($params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME])
            && $params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME]
        ) {
            $token = $params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME];
            $customerData = $this->customerDataBuilder->build($params);

            if ($customerData[SubscriberInterface::CUSTOMER_ID] || $customerData[SubscriberInterface::VISITOR_ID]) {
                $subscriber = $this->subscriberRepository->getByCustomerVisitor(
                    $customerData[SubscriberInterface::CUSTOMER_ID],
                    $customerData[SubscriberInterface::VISITOR_ID]
                );
                if (!$subscriber) {
                    $subscriber = $this->subscriberFactory->create();
                }
                $subscriber->setStoreId($this->storeManager->getStore()->getId());

                if ($subscriber->getToken() != $token) {
                    $subscriber->addData($customerData);
                    $this->resetToken($subscriber, $token);
                } else {
                    $subscriber->addData($customerData);
                    $this->subscriberRepository->save($subscriber);
                }
            }
        }

        return $this;
    }

    /**
     * @param SubscriberInterface $subscriber
     * @param string $newToken
     *
     * @return $this
     *
     * @throws CouldNotSaveException
     */
    public function resetToken(SubscriberInterface $subscriber, $newToken)
    {
        $subscriber->setToken($newToken);
        $this->subscriberRepository->save($subscriber);

        return $this;
    }
}
