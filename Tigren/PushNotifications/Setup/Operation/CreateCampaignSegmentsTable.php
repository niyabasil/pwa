<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Setup\Operation;

use Tigren\PushNotifications\Api\Data\CampaignSegmentsInterface;
use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Model\ResourceModel\CampaignSegments;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class CreateCampaignSegmentsTable
 */
class CreateCampaignSegmentsTable
{
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
        $mainTable = $setup->getTable(CampaignSegments::TABLE_NAME);
        $campaignTable = $setup->getTable(CreateCampaignTable::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $mainTable
            )->setComment(
                'Tigren Push Notifications Campaign Customer Segments Table'
            )->addColumn(
                CampaignSegmentsInterface::CAMPAIGN_SEGMENT_ID,
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
                CampaignSegmentsInterface::CAMPAIGN_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Campaign ID'
            )->addColumn(
                CampaignSegmentsInterface::SEGMENT_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Customer Segment ID'
            )->addForeignKey(
                $setup->getFkName(
                    $mainTable,
                    CampaignSegmentsInterface::CAMPAIGN_ID,
                    $campaignTable,
                    CampaignInterface::CAMPAIGN_ID
                ),
                CampaignSegmentsInterface::CAMPAIGN_ID,
                $campaignTable,
                CampaignInterface::CAMPAIGN_ID,
                Table::ACTION_CASCADE
            );
    }
}
