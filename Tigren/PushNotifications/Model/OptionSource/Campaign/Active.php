<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Active
 * @package Tigren\PushNotifications\Model\OptionSource\Campaign
 */
class Active implements OptionSourceInterface
{
    /**
     *
     */
    const STATUS_ACTIVE = 1;
    /**
     *
     */
    const STATUS_INACTIVE = 0;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::STATUS_ACTIVE => __("Active"),
            self::STATUS_INACTIVE => __("Inactive"),
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::STATUS_ACTIVE,
                'label' => __("Active")
            ],
            [
                'value' => self::STATUS_INACTIVE,
                'label' => __("Inactive")
            ],
        ];
    }
}
