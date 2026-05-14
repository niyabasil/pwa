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
 * Class PBHFooterLink
 * @package Tigren\PwaSampleData\Setup\Patch\Data
 */
class PBHFooterLink implements DataPatchInterface
{
    const BLOCK_IDENTIFIER = 'pbh-footer-link';

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
            'title' => 'PBH Footer Link',
            'identifier' => self::BLOCK_IDENTIFIER,
            'content' => '<style>#html-body [data-pb-style=G234E64],#html-body [data-pb-style=JWBQ19M],#html-body [data-pb-style=VHEAL9H]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=G234E64],#html-body [data-pb-style=JWBQ19M]{width:66.6667%;align-self:stretch}#html-body [data-pb-style=G234E64]{width:33.3333%}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div class="footer-links-content" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="VHEAL9H"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="12" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="JWBQ19M"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="footer-links-col"&gt;
    &lt;div class="block-title"&gt;
        &lt;p&gt;&lt;strong&gt;{{trans "Information and Customer Service Center"}}&lt;/strong&gt;&lt;/p&gt;
    &lt;/div&gt;
    &lt;div class="footer-links"&gt;
        &lt;div class="footer-link"&gt;
            &lt;p&gt;&lt;strong&gt;{{trans "Tigren PWA"}}&lt;/strong&gt;&lt;/p&gt;
            &lt;ul class="links"&gt;
                &lt;li class="nav item"&gt;&lt;a href="/about-us"&gt;{{trans "About Us"}}&lt;/a&gt;&lt;/li&gt;
                &lt;li class="nav item"&gt;&lt;a href="/member_news" data-action="open-contact-us-modal"&gt;{{trans "Member News"}}&lt;/a&gt;&lt;/li&gt;
                &lt;li class="nav item"&gt;&lt;a href="#"&gt;{{trans "Special Privileges For Members"}}&lt;/a&gt;&lt;/li&gt;
            &lt;/ul&gt;
        &lt;/div&gt;
        &lt;div class="footer-link"&gt;
            &lt;p&gt;&lt;strong&gt;{{trans "customer service"}}&lt;/strong&gt;&lt;/p&gt;
            &lt;ul class="links"&gt;
                &lt;li class="nav item"&gt;&lt;a href="#"&gt;{{trans "Discount Coupon"}}&lt;/a&gt;&lt;/li&gt;
                &lt;li class="nav item"&gt;&lt;a href="#" data-action="open-contact-us-modal"&gt;{{trans "FAQ"}}&lt;/a&gt;&lt;/li&gt;
                &lt;li class="nav item"&gt;&lt;a href="#"&gt;{{trans "Warranty Terms"}}&lt;/a&gt;&lt;/li&gt;
            &lt;/ul&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/div&gt;</div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="G234E64"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="footer-contact"&gt;
    &lt;p&gt;&lt;strong&gt;{{trans "Contact Us"}}&lt;/strong&gt;&lt;/p&gt;
    &lt;ul class="links"&gt;
        &lt;li class="nav address icon-sprite-after"&gt;{{trans "130 Nguyen Duc Canh, Hoang Mai, Ha Noi"}}&lt;/li&gt;
        &lt;li class="nav phone icon-sprite-after"&gt;&lt;a href="tel:+84988358143"&gt;{{trans "+84 988 358 143"}}&lt;/a&gt;&lt;/li&gt;
    &lt;/ul&gt;
&lt;/div&gt;</div></div></div></div></div>',
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
