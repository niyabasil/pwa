<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel\Subscriber;

use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Active;
use Tigren\PushNotifications\Model\ResourceModel\Subscriber;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zend_Db_Select;

/**
 * @method \Tigren\PushNotifications\Model\Subscriber[] getItems()
 * @method Subscriber getResource()
 */
class Collection extends AbstractCollection
{
    /**
     *
     */
    const CACHE_TAG = 'tigren_notifications_subscriber';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \Tigren\PushNotifications\Model\Subscriber::class,
            Subscriber::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter(
            'main_table.' . SubscriberInterface::IS_ACTIVE,
            [
                'eq' => Active::STATUS_ACTIVE
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function getTokensOrderedByStore()
    {
        $this->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns([
                SubscriberInterface::TOKEN,
                SubscriberInterface::STORE_ID
            ])
            ->order(SubscriberInterface::STORE_ID);

        return $this;
    }
}
