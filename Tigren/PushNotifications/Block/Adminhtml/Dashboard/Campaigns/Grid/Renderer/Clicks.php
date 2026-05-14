<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer;

use Tigren\PushNotifications\Model\OptionSource\Campaign\Status;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Clicks
 * @package Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer
 */
class Clicks extends AbstractRenderer
{
    /**
     * Render action
     *
     * @param DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        if ($row->getStatus() == Status::STATUS_PASSED) {
            $clickedPercent = $row->getShownCounter() !== 0
                ? ($row->getClickedCounter() / $row->getShownCounter()) * 100
                : 0;

            $resultRow = __('%1 (%2%)', $row->getClickedCounter(), number_format($clickedPercent, 2));
        } else {
            $resultRow = __('Scheduled');
        }

        return $resultRow;
    }
}
