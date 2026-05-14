<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class Subscriber
 * @package Tigren\PushNotifications\Controller\Adminhtml
 */
abstract class Subscriber extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Tigren_PushNotifications::notifications_subscriber';
}
