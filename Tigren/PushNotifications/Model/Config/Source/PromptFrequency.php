<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PromptFrequency
 * @package Tigren\PushNotifications\Model\Config\Source
 */
class PromptFrequency implements OptionSourceInterface
{
    /**
     *
     */
    const FREQUENCY_EVERY_TIME = 0;
    /**
     *
     */
    const FREQUENCY_HOURLY = 1;
    /**
     *
     */
    const FREQUENCY_DAILY = 2;
    /**
     *
     */
    const FREQUENCY_WEEKLY = 3;

    /**
     * @var array|null
     */
    protected $options;

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                self::FREQUENCY_EVERY_TIME => __('Every time'),
                self::FREQUENCY_HOURLY => __('Hourly'),
                self::FREQUENCY_DAILY => __('Daily'),
                self::FREQUENCY_WEEKLY => __('Weekly'),
            ];
        }

        return $this->options;
    }
}
