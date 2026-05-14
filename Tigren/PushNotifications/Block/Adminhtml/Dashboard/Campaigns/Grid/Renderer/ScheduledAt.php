<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer;

use Tigren\PushNotifications\Model\Builder\DateTimeBuilder;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Status;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class ScheduledAt
 * @package Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer
 */
class ScheduledAt extends AbstractRenderer
{
    /**
     * @var DateTimeBuilder
     */
    private $dateTimeBuilder;

    /**
     * @param Context $context
     * @param DateTimeBuilder $dateTimeBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        DateTimeBuilder $dateTimeBuilder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dateTimeBuilder = $dateTimeBuilder;
    }

    /**
     * Render action
     *
     * @param DataObject $row
     * @return string
     * @throws \Exception
     */
    public function render(DataObject $row)
    {
        return $this->dateTimeBuilder->getScheduledDateFromDifference($row);
    }
}
