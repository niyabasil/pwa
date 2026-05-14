<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\PushNotifications\Ui\DataProvider\Listing;

use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\ResourceModel\Subscriber\Collection;
use Tigren\PushNotifications\Api\SubscriberRepositoryInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class SubscriberDataProvider
 * @package Tigren\PushNotifications\Ui\DataProvider\Listing
 */
class SubscriberDataProvider extends AbstractDataProvider
{
    /**
     * @var SubscriberRepositoryInterface
     */
    private $repository;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Collection $collection
     * @param SubscriberRepositoryInterface $repository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        Collection $collection,
        SubscriberRepositoryInterface $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->repository = $repository;
    }

    /**
     * Get data
     *
     * @return array
     *
     * @throws NotificationException
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as $key => $subscriber) {
            $subscriberData = $this->repository->getById($subscriber[SubscriberInterface::SUBSCRIBER_ID])->getData();
            if (!$subscriberData[SubscriberInterface::CUSTOMER_ID]) {
                $subscriberData[SubscriberInterface::CUSTOMER_ID] = __('Guest');
            }
            $data['items'][$key] = $subscriberData;
        }

        return $data;
    }
}
