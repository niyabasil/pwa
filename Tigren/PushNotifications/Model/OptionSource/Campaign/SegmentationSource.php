<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Manager;

/**
 * Class SegmentationSource
 * @package Tigren\PushNotifications\Model\OptionSource\Campaign
 */
class SegmentationSource implements OptionSourceInterface
{
    /**
     *
     */
    const CUSTOMER_GROUPS = 0;
    /**
     *
     */
    const CUSTOMER_SEGMENTS = 1;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $result = [
            [
                'value' => self::CUSTOMER_GROUPS,
                'label' => __('Use Customer Groups (Default)')
            ],
            [
                'disabled' => !$this->moduleManager->isEnabled('Tigren_Segments'), //field flag used in options.js
                'value' => self::CUSTOMER_SEGMENTS,
                'label' => __('Use Tigren Customer Segments')
            ]
        ];

        return $result;
    }
}
