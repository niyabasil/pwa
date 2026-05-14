<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model;

use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\Segments\Model\ResourceModel\Index;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class will be used as proxy in CampaignProcessor
 * due to operating with Tigren Customer Segments module
 * which can be not installed
 */
class CustomerSegmentsValidator
{
    /**
     * @var SubscriberRepository
     */
    private $subscriberRepository;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        SubscriberRepository $subscriberRepository,
        ObjectManagerInterface $objectManager
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param array $subscriberToken
     * @param CampaignSegments[] $segments
     *
     * @throws NotificationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateSegments(&$subscriberToken, $segments)
    {
        if (empty($segments)) {
            return;
        }
        $segmentIds = [];

        foreach ($segments as $segment) {
            $segmentIds[] = $segment->getSegmentId();
        }
        foreach ($subscriberToken as $key => $token) {
            $subscriber = $this->subscriberRepository->getByToken($token);

            if ($customerId = (int)$subscriber->getCustomerId()) {
                if (empty($this->objectManager->create(Index::class)
                    ->checkValidCustomerFromIndex($segment->getSegmentId(), $customerId, 'customer_id'))
                ) {
                    unset($subscriberToken[$key]);
                }
            } else {
                unset($subscriberToken[$key]);
            }
        }
    }
}
