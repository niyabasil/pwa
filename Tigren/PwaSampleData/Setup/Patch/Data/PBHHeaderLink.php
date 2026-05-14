<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\PwaSampleData\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * Class PBHHeaderLink
 * @package Tigren\PwaSampleData\Setup\Patch\Data
 */
class PBHHeaderLink implements DataPatchInterface
{
    const BLOCK_IDENTIFIER = 'pbh-header-link';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        BlockFactory $blockFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function apply()
    {
        $newCmsBlock = [
            'title' => 'PBH Header Link',
            'identifier' => self::BLOCK_IDENTIFIER,
            'content' => '<div data-content-type="html" data-appearance="default" data-element="main">&lt;ul class="header-links"&gt;
    &lt;li&gt;{{trans "Contact Us Call"}} : &lt;a href="tel:+84988358143"&gt;{{trans "+84 988 358 143"}}&lt;/a&gt;&lt;/li&gt;
    &lt;li&gt;{{trans "Email"}} : &lt;a href="mailto:info@tigren.com"&gt;{{trans "info@tigren.com"}}&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;</div>',
            'is_active' => 1,
            'stores' => 1
        ];

        $this->moduleDataSetup->startSetup();
        /** @var \Magento\Cms\Model\Block $block */
        $block = $this->blockFactory
            ->create()
            ->load($newCmsBlock['identifier'], 'identifier');

        /**
         * Create the block if it does not exists
         */
        if (!$block->getId()) {
            $block->setData($newCmsBlock)->save();
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        /**
         * Aliases are useful if we change the name of the patch until then we do not need any
         */
        return [];
    }
}
