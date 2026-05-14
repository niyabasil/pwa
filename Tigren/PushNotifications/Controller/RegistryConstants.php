<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller;

/**
 * Class RegistryConstants
 * @package Tigren\PushNotifications\Controller
 */
class RegistryConstants
{
    /**
     *
     */
    const USER_FIREBASE_TOKEN_PARAMS_KEY_NAME = 'userToken';
    /**
     *
     */
    const CAMPAIGN_ID_PARAMS_KEY_NAME = 'campaignId';
    /**
     *
     */
    const FIREBASE_SUBSCRIBE_URL_PATH = 'tigren_notifications/firebase/subscribe';
    /**
     *
     */
    const FIREBASE_CLICK_COUNTER_URL_PATH = 'tigren_notifications/firebase/counter';
    /**
     *
     */
    const CLICK_COUNTER_FLAG_PARAM_NAME = 'amcounter';
    /**
     *
     */
    const CLICK_COUNTER_URL_PATH_PARAM_NAME = 'counterUrlPath';
    /**
     *
     */
    const REGISTRY_TEST_NOTIFICATION_NAME = 'tigren-notification-test';
}
