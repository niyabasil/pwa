<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Tigren\PushNotifications\Model\OptionSource\Campaign
 */
class Status implements ArrayInterface
{
    /**
     *
     */
    const STATUS_PASSED = 0;
    /**
     *
     */
    const STATUS_SCHEDULED = 1;
    /**
     *
     */
    const STATUS_EDITED = 2;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STATUS_PASSED, 'label' => __('Passed')],
            ['value' => self::STATUS_SCHEDULED, 'label' => __('Scheduled')],
            ['value' => self::STATUS_EDITED, 'label' => __('Edited')]
        ];
    }
}
