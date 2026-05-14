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
 * Class PBHHeaderPromo
 * @package Tigren\PwaSampleData\Setup\Patch\Data
 */
class PBHHeaderPromo implements DataPatchInterface
{
    const BLOCK_IDENTIFIER = 'pbh-header-promo';

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
            'title' => 'PBH Header Promo',
            'identifier' => self::BLOCK_IDENTIFIER,
            'content' => '<style>#html-body [data-pb-style=JET0SJA]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="JET0SJA"><div data-content-type="html" data-appearance="default" data-element="main">&lt;span&gt;FREE SHIPPING on all orders over $100&lt;/span&gt;</div></div></div>',
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
