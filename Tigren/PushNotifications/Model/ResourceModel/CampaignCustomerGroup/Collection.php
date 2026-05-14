<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel\CampaignCustomerGroup;

use Tigren\PushNotifications\Model\ResourceModel\CampaignCustomerGroup;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Tigren\PushNotifications\Model\CampaignCustomerGroup::class,
            CampaignCustomerGroup::class
        );
    }
}
