<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel;

use Tigren\PushNotifications\Api\Data\CampaignSegmentsInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CampaignSegments
 */
class CampaignSegments extends AbstractDb
{
    /**
     *
     */
    const TABLE_NAME = 'tigren_notifications_campaign_segments';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CampaignSegmentsInterface::CAMPAIGN_SEGMENT_ID);
    }
}
