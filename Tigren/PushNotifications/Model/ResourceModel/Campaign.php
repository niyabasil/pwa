<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Setup\Operation\CreateCampaignStoreTable;
use Tigren\PushNotifications\Setup\Operation\CreateCampaignTable;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * Class Campaign
 * @package Tigren\PushNotifications\Model\ResourceModel
 */
class Campaign extends AbstractDb
{
    public function _construct()
    {
        $this->_init(CreateCampaignTable::TABLE_NAME, CampaignInterface::CAMPAIGN_ID);
    }

    /**
     * @param $id
     * @return array
     */
    public function getStoreIds($id)
    {
        $select = $this->getConnection()->select()->from(
            ['c' => $this->getTable(CreateCampaignStoreTable::TABLE_NAME)],
            ['store_id']
        )->where(
            'campaign_id = :campaign_id'
        );
        $bind = ['campaign_id' => (int)$id];

        return $this->getConnection()->fetchCol($select, $bind);
    }
}
