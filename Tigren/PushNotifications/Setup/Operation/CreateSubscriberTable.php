<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Setup\Operation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Zend_Db_Exception;

/**
 * Class CreateSubscriberTable
 * @package Tigren\PushNotifications\Setup\Operation
 */
class CreateSubscriberTable
{
    /**
     *
     */
    const TABLE_NAME = 'tigren_notifications_subscriber';

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createSubscriberTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     *
     * @throws Zend_Db_Exception
     */
    private function createSubscriberTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(self::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Tigren Push Notifications Subscriber table'
            )->addColumn(
                SubscriberInterface::SUBSCRIBER_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Subscriber Id'
            )->addColumn(
                SubscriberInterface::SOURCE,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null,
                    'nullable' => true
                ],
                'Source'
            )->addColumn(
                SubscriberInterface::IS_ACTIVE,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'default' => 0,
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Is Active'
            )->addColumn(
                SubscriberInterface::SUBSCRIBER_IP,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null,
                    'nullable' => true
                ],
                'Subscribers IP'
            )->addColumn(
                SubscriberInterface::TOKEN,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null,
                    'nullable' => true
                ],
                'Token'
            )->addColumn(
                SubscriberInterface::LOCATION,
                Table::TYPE_TEXT,
                255,
                [
                    'default' => null,
                    'nullable' => true
                ],
                'Location'
            )->addColumn(
                SubscriberInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'default' => null,
                    'nullable' => true
                ],
                'Customer ID'
            )->addColumn(
                SubscriberInterface::VISITOR_ID,
                Table::TYPE_BIGINT,
                null,
                [
                    'default' => null,
                    'nullable' => true
                ],
                'Customer ID'
            )->addColumn(
                SubscriberInterface::CREATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Created At'
            )->addColumn(
                SubscriberInterface::UPDATED_AT,
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE
                ],
                'Updated At'
            )->addColumn(
                SubscriberInterface::STORE_ID,
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Store Id'
            );
    }
}
