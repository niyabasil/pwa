<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\PushNotificationsGraphQl\Model\Resolver;

use Tigren\PushNotifications\Model\GetCustomerIp;
use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Api\SubscriberRepositoryInterface;
use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Lib\Browser;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Active;
use Tigren\PushNotifications\Model\SubscriberFactory;
use Exception;
use Magento\Customer\Model\Visitor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\HTTP\Header;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Subscribe
 * @package Tigren\PushNotificationsGraphQl\Model\Resolver
 */
class Subscribe implements ResolverInterface
{
    /**
     * @var Header
     */
    private $httpHeader;

    /**
     * @var Visitor
     */
    private $visitor;

    /**
     * @var GetCustomerIp
     */
    private $customerIp;

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

    /**
     * Subscribe constructor.
     *
     * @param Header $httpHeader
     * @param Visitor $visitor
     * @param GetCustomerIp $customerIp
     * @param SubscriberRepositoryInterface $subscriberRepository
     * @param SubscriberFactory $subscriberFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Header $httpHeader,
        Visitor $visitor,
        GetCustomerIp $customerIp,
        SubscriberRepositoryInterface $subscriberRepository,
        SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->httpHeader = $httpHeader;
        $this->visitor = $visitor;
        $this->customerIp = $customerIp;
        $this->subscriberRepository = $subscriberRepository;
        $this->subscriberFactory = $subscriberFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     * @throws GraphQlAuthorizationException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($args['token'])) {
            throw new GraphQlInputException(__('The token is missing.'));
        }

        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $customerId = $context->getUserId();
        if (!$customerId) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        try {
            $this->process($args['token'], $customerId);
            $result = [
                'result' => true,
                'message' => __('You are subscribed successfully'),
            ];
        } catch (Exception $exception) {
            $result = [
                'result' => true,
                'message' => $exception->getMessage(),
            ];
        }

        return $result;
    }

    /**
     * Process Subscribe
     *
     * @param string $userToken
     * @param int $customerId
     * @throws CouldNotSaveException
     * @throws NotificationException|NoSuchEntityException
     * @throws Exception
     */
    private function process(string $userToken, int $customerId)
    {
        if (isset($userToken) && $userToken) {
            $customerData = $this->build($userToken, $customerId);
            if ($customerData[SubscriberInterface::CUSTOMER_ID] || $customerData[SubscriberInterface::VISITOR_ID]) {
                $subscriber = $this->subscriberRepository->getByCustomerVisitor(
                    $customerData[SubscriberInterface::CUSTOMER_ID],
                    $customerData[SubscriberInterface::VISITOR_ID]
                );
                if (!$subscriber) {
                    $subscriber = $this->subscriberFactory->create();
                }
                $subscriber->setStoreId($this->storeManager->getStore()->getId());

                if ($subscriber->getToken() != $userToken) {
                    $subscriber->addData($customerData);
                    $this->resetToken($subscriber, $userToken);
                } else {
                    $subscriber->addData($customerData);
                    $this->subscriberRepository->save($subscriber);
                }
            }
        }
    }

    /**
     * Build Customer Data
     *
     * @param string $userToken
     * @param int $customerId
     * @return array
     * @throws Exception
     */
    private function build(string $userToken, int $customerId)
    {
        $currentUserIp = $this->customerIp->getCurrentIp();
        return [
            SubscriberInterface::SOURCE => $this->getBrowserFromUserAgent(),
            SubscriberInterface::LOCATION => '',
            SubscriberInterface::VISITOR_ID => $this->visitor->getId() ?: null,
            SubscriberInterface::CUSTOMER_ID => $customerId ?: null,
            SubscriberInterface::SUBSCRIBER_IP => $currentUserIp,
            SubscriberInterface::TOKEN => $userToken,
            SubscriberInterface::IS_ACTIVE => Active::STATUS_ACTIVE,
        ];
    }

    /**
     * Get Browser From UserAgent
     *
     * @return string
     */
    private function getBrowserFromUserAgent()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $browserDetector = new Browser($userAgent);
        $currentBrowser = $browserDetector->getBrowser();

        return $currentBrowser ?: '';
    }

    /**
     * Reset Token
     *
     * @param SubscriberInterface $subscriber
     * @param string $newToken
     * @return $this
     * @throws CouldNotSaveException
     */
    private function resetToken(SubscriberInterface $subscriber, string $newToken)
    {
        $subscriber->setToken($newToken);
        $this->subscriberRepository->save($subscriber);

        return $this;
    }
}
