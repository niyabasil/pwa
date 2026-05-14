<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Setup\Patch\Data;

use Exception;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class UpdateProductAttributesPatch
 * @package Tigren\Pwa\Setup\Patch\Data
 */
class UpdateProductAttributesPatch implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var string[]
     */
    private $attributes = [
        'visibility'
    ];

    /**
     * CreateCategoryCmsPagesPatch constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        foreach ($this->attributes as $attribute) {
            $this->updateAttribute($attribute, $eavSetup);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Update Attribute
     *
     * @param string $attribute
     * @param EavSetup $eavSetup
     */
    private function updateAttribute(string $attribute, EavSetup $eavSetup): void
    {
        try {
            $eavSetup->updateAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                $attribute,
                'used_in_product_listing',
                '1'
            );
            $eavSetup->updateAttribute(
                ProductAttributeInterface::ENTITY_TYPE_CODE,
                $attribute,
                'is_user_defined',
                '1'
            );
        } catch (Exception $e) {
            // do something here
        }
    }

    /**
     * @inheritDoc
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public static function getVersion()
    {
        return '1.0.0';
    }
}
