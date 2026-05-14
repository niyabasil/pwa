<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Builder;

use Tigren\PushNotifications\Exception\NotificationException;

/**
 *
 */
interface BuilderInterface
{
    /**
     * @param array $params
     * @return array|string
     * @throws NotificationException
     */
    public function build(array $params);
}
