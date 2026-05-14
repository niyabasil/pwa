<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Api;

use Tigren\PushNotifications\Exception\NotificationException;

/**
 *
 */
interface SenderInterface
{
    /**
     * @param array $params
     * @param int|null $storeId
     *
     * @return array
     *
     * @throws NotificationException
     */
    public function send(array $params, $storeId = null);
}
