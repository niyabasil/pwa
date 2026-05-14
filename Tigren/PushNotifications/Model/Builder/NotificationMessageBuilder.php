<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Builder;

/**
 * Class NotificationMessageBuilder
 * @package Tigren\PushNotifications\Model\Builder
 */
class NotificationMessageBuilder implements BuilderInterface
{
    /**
     *
     */
    const SUCCESS_STATUS = 1;

    /**
     * @inheritdoc
     */
    public function build(array $params)
    {
        $result = '';

        if (isset($params['status'])) {
            $status = (int)$params['status'];

            if ($status === self::SUCCESS_STATUS) {
                $result = __('Notification has been sent.');
            } else {
                $result = __('Notification send error.');
            }
        }

        return $result;
    }
}
