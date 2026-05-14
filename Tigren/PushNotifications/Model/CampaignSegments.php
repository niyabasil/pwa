<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model;

use Tigren\PushNotifications\Api\Data\CampaignSegmentsInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CampaignSegments
 */
class CampaignSegments extends AbstractModel implements CampaignSegmentsInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\CampaignSegments::class);
        $this->setIdFieldName(CampaignSegmentsInterface::CAMPAIGN_SEGMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function getCampaignSegmentId()
    {
        return (int)$this->_getData(CampaignSegmentsInterface::CAMPAIGN_SEGMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCampaignSegmentId($id)
    {
        return $this->setData(CampaignSegmentsInterface::CAMPAIGN_SEGMENT_ID, (int)$id);
    }

    /**
     * @inheritdoc
     */
    public function getSegmentId()
    {
        return (int)$this->_getData(CampaignSegmentsInterface::SEGMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setSegmentId($id)
    {
        return $this->setData(CampaignSegmentsInterface::SEGMENT_ID, (int)$id);
    }

    /**
     * @inheritdoc
     */
    public function getCampaignId()
    {
        return (int)$this->_getData(CampaignSegmentsInterface::CAMPAIGN_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCampaignId($id)
    {
        return $this->setData(CampaignSegmentsInterface::CAMPAIGN_ID, (int)$id);
    }
}
