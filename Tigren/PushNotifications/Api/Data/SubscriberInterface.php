<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Api\Data;

/**
 *
 */
interface SubscriberInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const SUBSCRIBER_ID = 'subscriber_id';
    /**
     *
     */
    const SOURCE = 'source';
    /**
     *
     */
    const IS_ACTIVE = 'is_active';
    /**
     *
     */
    const SUBSCRIBER_IP = 'subscriber_ip';
    /**
     *
     */
    const TOKEN = 'token';
    /**
     *
     */
    const LOCATION = 'location';
    /**
     *
     */
    const VISITOR_ID = 'visitor_id';
    /**
     *
     */
    const CUSTOMER_ID = 'customer_id';
    /**
     *
     */
    const CREATED_AT = 'created_at';
    /**
     *
     */
    const UPDATED_AT = 'updated_at';
    /**
     *
     */
    const STORE_ID = 'store_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getSubscriberId();

    /**
     * @param int $subscriberId
     *
     * @return SubscriberInterface
     */
    public function setSubscriberId($subscriberId);

    /**
     * @return string
     */
    public function getSource();

    /**
     * @param string $source
     *
     * @return SubscriberInterface
     */
    public function setSource($source);

    /**
     * @return string
     */
    public function getSubscribersIp();

    /**
     * @param string $subscribersIp
     *
     * @return SubscriberInterface
     */
    public function setSubscribersIp($subscribersIp);

    /**
     * @return int|string
     */
    public function getIsActive();

    /**
     * @param int|string $active
     *
     * @return SubscriberInterface
     */
    public function setIsActive($active);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param string $token
     *
     * @return SubscriberInterface
     */
    public function setToken($token);

    /**
     * @return int
     */
    public function getLocation();

    /**
     * @param int|string $location
     *
     * @return SubscriberInterface
     */
    public function setLocation($location);

    /**
     * @return int
     */
    public function getVisitorId();

    /**
     * @param int $visitorId
     *
     * @return SubscriberInterface
     */
    public function setVisitorId($visitorId);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return SubscriberInterface
     */
    public function setCustomerId($customerId);


    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return SubscriberInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return SubscriberInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return SubscriberInterface
     */
    public function setStoreId($storeId);
}
