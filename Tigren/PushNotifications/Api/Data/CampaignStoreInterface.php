<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Api\Data;

use Tigren\PushNotifications\Model\CampaignStore;

/**
 *
 */
interface CampaignStoreInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    /**
     *
     */
    const STORE_ID = 'store_id';
    /**
     *
     */
    const CAMPAIGN_ID = 'campaign_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return CampaignStore
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return CampaignStore
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param $campaignId
     *
     * @return CampaignStore
     */
    public function setCampaignId($campaignId);
}
