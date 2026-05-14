<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Processor;

use Tigren\PushNotifications\Exception\NotificationException;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 *
 */
interface ProcessorInterface
{
    /**
     * @param array $params
     * @param int|null $storeId
     *
     * @return array
     *
     * @throws NotificationException
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws AlreadyExistsException
     */
    public function process(array $params, $storeId = null);
}
