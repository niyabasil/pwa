<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel;

use Tigren\PushNotifications\Api\Data\CampaignCustomerGroupInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class CampaignCustomerGroup
 */
class CampaignCustomerGroup extends AbstractDb
{
    /**
     *
     */
    const TABLE_NAME = 'tigren_notifications_campaign_group';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CampaignCustomerGroupInterface::CAMPAIGN_GROUP_ID);
    }
}
