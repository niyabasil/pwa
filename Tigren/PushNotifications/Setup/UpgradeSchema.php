<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Setup;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Setup\Operation\CreateCampaignSegmentsTable;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\CreateCampaignCustomerGroupTable
     */
    private $createCampaignCustomerGroupTable;

    /**
     * @var CreateCampaignSegmentsTable
     */
    private $createCampaignSegmentsTable;

    public function __construct(
        Operation\CreateCampaignCustomerGroupTable $createCampaignCustomerGroupTable,
        Operation\CreateCampaignSegmentsTable $createCampaignSegmentsTable
    ) {
        $this->createCampaignCustomerGroupTable = $createCampaignCustomerGroupTable;
        $this->createCampaignSegmentsTable = $createCampaignSegmentsTable;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (!$context->getVersion() || version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->createCampaignCustomerGroupTable->execute($setup);
            $this->createCampaignSegmentsTable->execute($setup);
            $this->addSegmentationColumn($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function addSegmentationColumn($setup)
    {
        $column = [
            'type' => Table::TYPE_BOOLEAN,
            'nullable' => false,
            'comment' => 'Segmentation Source Column',
            'default' => 0
        ];

        $setup->getConnection()->addColumn(
            $setup->getTable(Operation\CreateCampaignTable::TABLE_NAME),
            CampaignInterface::SEGMENTATION_SOURCE,
            $column
        );
    }
}
