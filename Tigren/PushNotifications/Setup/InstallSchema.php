<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package Tigren\PushNotifications\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Operation\CreateCampaignTable
     */
    private $campaignTable;

    /**
     * @var Operation\CreateSubscriberTable
     */
    private $subscriberTable;

    /**
     * @var Operation\CreateCampaignStoreTable
     */
    private $campaignStoreTable;

    public function __construct(
        Operation\CreateCampaignTable $campaignTable,
        Operation\CreateSubscriberTable $subscriberTable,
        Operation\CreateCampaignStoreTable $campaignStoreTable
    ) {
        $this->campaignTable = $campaignTable;
        $this->subscriberTable = $subscriberTable;
        $this->campaignStoreTable = $campaignStoreTable;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->campaignTable->execute($setup);
        $this->subscriberTable->execute($setup);
        $this->campaignStoreTable->execute($setup);
        $setup->endSetup();
    }
}
