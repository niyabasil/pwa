<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Api;

use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Exception\NotificationException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface SubscriberRepositoryInterface
{
    /**
     * Save
     *
     * @param SubscriberInterface $subscriber
     * @return SubscriberInterface
     * @throws CouldNotSaveException
     */
    public function save(SubscriberInterface $subscriber);

    /**
     * Get by id
     *
     * @param int $subscriberId
     * @return SubscriberInterface
     * @throws NotificationException
     */
    public function getById($subscriberId);

    /**
     * Get by token
     *
     * @param string $token
     * @return SubscriberInterface
     * @throws NotificationException
     */
    public function getByToken($token);

    /**
     * Get by Customer Id or Visitor Id
     *
     * @param string $customerId
     * @param string $visitorId
     * @return SubscriberInterface
     * @throws NotificationException
     */
    public function getByCustomerVisitor($customerId, $visitorId);

    /**
     * Delete
     *
     * @param SubscriberInterface $subscriber
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(SubscriberInterface $subscriber);

    /**
     * Delete by id
     *
     * @param int $subscriberId
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function deleteById($subscriberId);

    /**
     * Lists
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
