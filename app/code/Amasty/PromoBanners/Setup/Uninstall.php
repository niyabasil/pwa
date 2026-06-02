<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @param SchemaSetupInterface $installer
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(SchemaSetupInterface $installer, ModuleContextInterface $context): void
    {
        $installer->startSetup();

        $installer->getConnection()->dropTable($installer->getTable('amasty_banner_rule'));
        $installer->getConnection()->dropTable($installer->getTable('amasty_banner_attribute'));
        $installer->getConnection()->dropTable($installer->getTable('amasty_banner_rule_products'));

        $installer->endSetup();
    }
}
