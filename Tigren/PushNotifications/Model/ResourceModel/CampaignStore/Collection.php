<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel\CampaignStore;

use Tigren\PushNotifications\Model\ResourceModel\CampaignStore;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Tigren\PushNotifications\Model\ResourceModel\CampaignStore
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected $_idFieldName = 'id';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(
            \Tigren\PushNotifications\Model\CampaignStore::class,
            CampaignStore::class
        );
    }
}
