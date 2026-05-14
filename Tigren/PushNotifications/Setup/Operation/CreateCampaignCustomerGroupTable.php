<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Setup\Operation;

use Tigren\PushNotifications\Api\Data\CampaignCustomerGroupInterface;
use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Model\ResourceModel\CampaignCustomerGroup;
use Magento\Customer\Model\Group;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class CreateCampaignCustomerGroupTable
 */
class CreateCampaignCustomerGroupTable
{
    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    public function __construct(ProductMetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @return Table
     * @throws Zend_Db_Exception
     */
    private function createTable($setup)
    {
        $mainTable = $setup->getTable(CampaignCustomerGroup::TABLE_NAME);
        $campaignTable = $setup->getTable(CreateCampaignTable::TABLE_NAME);
        $groupsTable = $setup->getTable(Group::ENTITY);

        return $setup->getConnection()
            ->newTable(
                $mainTable
            )->setComment(
                'Tigren Push Notifications Campaign Customer Groups Table'
            )->addColumn(
                CampaignCustomerGroupInterface::CAMPAIGN_GROUP_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Entity ID'
            )->addColumn(
                CampaignCustomerGroupInterface::CAMPAIGN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Campaign ID'
            )->addColumn(
                CampaignCustomerGroupInterface::GROUP_ID,
                version_compare($this->metadata->getVersion(), '2.2.0', '<')
                    ? Table::TYPE_SMALLINT
                    : Table::TYPE_INTEGER,
                version_compare($this->metadata->getVersion(), '2.2.0', '<')
                    ? 5
                    : 10,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Customer Group ID'
            )->addForeignKey(
                $setup->getFkName(
                    $mainTable,
                    CampaignCustomerGroupInterface::GROUP_ID,
                    $groupsTable,
                    'customer_group_id'
                ),
                CampaignCustomerGroupInterface::GROUP_ID,
                $groupsTable,
                'customer_group_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $mainTable,
                    CampaignCustomerGroupInterface::CAMPAIGN_ID,
                    $campaignTable,
                    CampaignInterface::CAMPAIGN_ID
                ),
                CampaignCustomerGroupInterface::CAMPAIGN_ID,
                $campaignTable,
                CampaignInterface::CAMPAIGN_ID,
                Table::ACTION_CASCADE
            );
    }
}
