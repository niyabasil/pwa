<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Api\Data;

/**
 *
 */
interface CampaignCustomerGroupInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CAMPAIGN_GROUP_ID = 'campaign_group_id';
    /**
     *
     */
    const GROUP_ID = 'group_id';
    /**
     *
     */
    const CAMPAIGN_ID = 'campaign_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getCampaignGroupId();

    /**
     * @param int $id
     *
     * @return CampaignCustomerGroupInterface
     */
    public function setCampaignGroupId($id);

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $id
     *
     * @return CampaignCustomerGroupInterface
     */
    public function setGroupId($id);

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $id
     *
     * @return CampaignCustomerGroupInterface
     */
    public function setCampaignId($id);
}
