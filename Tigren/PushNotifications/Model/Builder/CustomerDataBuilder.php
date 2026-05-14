<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Builder;

use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Controller\RegistryConstants;
use Tigren\PushNotifications\Lib\Browser;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Active;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Visitor;
use Magento\Framework\HTTP\Header;
use Tigren\PushNotifications\Model\GetCustomerIp;

/**
 * Class CustomerDataBuilder
 * @package Tigren\PushNotifications\Model\Builder
 */
class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @var GetCustomerIp
     */
    private $customerIp;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Header
     */
    private $httpHeader;

    /**
     * @var Visitor
     */
    private $visitor;

    public function __construct(
        Session $customerSession,
        Header $httpHeader,
        Visitor $visitor,
        GetCustomerIp $customerIp
    ) {
        $this->customerSession = $customerSession;
        $this->httpHeader = $httpHeader;
        $this->visitor = $visitor;
        $this->customerIp = $customerIp;
    }

    /**
     * @inheritdoc
     */
    public function build(array $params)
    {
        $currentUserIp = $this->getCurrentIp();

        return [
            SubscriberInterface::SOURCE => $this->getBrowserFromUserAgent(),
            SubscriberInterface::LOCATION => '',
            SubscriberInterface::VISITOR_ID => $this->visitor->getId() ?: null,
            SubscriberInterface::CUSTOMER_ID => $this->customerSession->getCustomerId() ?: null,
            SubscriberInterface::SUBSCRIBER_IP => $currentUserIp,
            SubscriberInterface::TOKEN => $params[RegistryConstants::USER_FIREBASE_TOKEN_PARAMS_KEY_NAME],
            SubscriberInterface::IS_ACTIVE => Active::STATUS_ACTIVE,
        ];
    }

    /**
     * @return string
     */
    private function getCurrentIp()
    {
        return $this->customerIp->getCurrentIp();
    }

    /**
     * @return string
     */
    private function getBrowserFromUserAgent()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $browserDetector = new Browser($userAgent);
        $currentBrowser = $browserDetector->getBrowser();

        return $currentBrowser ?: '';
    }
}
