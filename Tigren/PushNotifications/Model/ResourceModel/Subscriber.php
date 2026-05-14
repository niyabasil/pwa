<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel;

use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Setup\Operation\CreateSubscriberTable;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\DB\Helper;
use Magento\Framework\DataObject;

/**
 * Class Subscriber
 * @package Tigren\PushNotifications\Model\ResourceModel
 */
class Subscriber extends AbstractDb
{
    /**
     * @var Helper
     */
    private $dbHelper;

    /**
     * @var DataObject
     */
    private $associatedQuestionEntityMap;

    /**
     * Question constructor.
     *
     * @param Context $context
     * @param Helper $dbHelper
     * @param DataObject $associatedQuestionEntityMap
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Helper $dbHelper,
        DataObject $associatedQuestionEntityMap,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->associatedQuestionEntityMap = $associatedQuestionEntityMap;
        $this->dbHelper = $dbHelper;
    }

    public function _construct()
    {
        $this->_init(CreateSubscriberTable::TABLE_NAME, SubscriberInterface::SUBSCRIBER_ID);
    }

    /**
     * @param string $entityType
     * @return array
     */
    public function getReferenceConfig($entityType = '')
    {
        return $this->associatedQuestionEntityMap->getData($entityType);
    }
}
