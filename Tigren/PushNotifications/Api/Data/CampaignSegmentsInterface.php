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
interface CampaignSegmentsInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CAMPAIGN_SEGMENT_ID = 'campaign_segment_id';
    /**
     *
     */
    const SEGMENT_ID = 'segment_id';
    /**
     *
     */
    const CAMPAIGN_ID = 'campaign_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getCampaignSegmentId();

    /**
     * @param int $id
     *
     * @return CampaignSegmentsInterface
     */
    public function setCampaignSegmentId($id);

    /**
     * @return int
     */
    public function getSegmentId();

    /**
     * @param int $id
     *
     * @return CampaignSegmentsInterface
     */
    public function setSegmentId($id);

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $id
     *
     * @return CampaignSegmentsInterface
     */
    public function setCampaignId($id);
}
