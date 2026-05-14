<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model;

use Tigren\PushNotifications\Api\Data\CampaignCustomerGroupInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CampaignCustomerGroup
 */
class CampaignCustomerGroup extends AbstractModel implements CampaignCustomerGroupInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\CampaignCustomerGroup::class);
        $this->setIdFieldName(CampaignCustomerGroupInterface::CAMPAIGN_GROUP_ID);
    }

    /**
     * @inheritdoc
     */
    public function getCampaignGroupId()
    {
        return (int)$this->_getData(CampaignCustomerGroupInterface::CAMPAIGN_GROUP_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCampaignGroupId($id)
    {
        return $this->setData(CampaignCustomerGroupInterface::CAMPAIGN_GROUP_ID, (int)$id);
    }

    /**
     * @inheritdoc
     */
    public function getGroupId()
    {
        return (int)$this->_getData(CampaignCustomerGroupInterface::GROUP_ID);
    }

    /**
     * @inheritdoc
     */
    public function setGroupId($id)
    {
        return $this->setData(CampaignCustomerGroupInterface::GROUP_ID, (int)$id);
    }

    /**
     * @inheritdoc
     */
    public function getCampaignId()
    {
        return (int)$this->_getData(CampaignCustomerGroupInterface::CAMPAIGN_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCampaignId($id)
    {
        return $this->setData(CampaignCustomerGroupInterface::CAMPAIGN_ID, (int)$id);
    }
}
