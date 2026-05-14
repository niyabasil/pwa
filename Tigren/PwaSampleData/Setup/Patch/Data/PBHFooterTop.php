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
 * Class PBHFooterTop
 * @package Tigren\PwaSampleData\Setup\Patch\Data
 */
class PBHFooterTop implements DataPatchInterface
{
    const BLOCK_IDENTIFIER = 'pbh-footer-top';

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
            'title' => 'PBH Footer Top',
            'identifier' => self::BLOCK_IDENTIFIER,
            'content' => '<style>#html-body [data-pb-style=AWRH1IP]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=GXJHG2C],#html-body [data-pb-style=IEAM0YQ],#html-body [data-pb-style=OKTV66C],#html-body [data-pb-style=OUMISHA],#html-body [data-pb-style=YYKNR87]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:20%;align-self:stretch}</style><div data-content-type="row" data-appearance="contained" data-element="main"><div class="footer-top" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src="" data-element="inner" data-pb-style="AWRH1IP"><div class="pagebuilder-column-group" style="display: flex;" data-content-type="column-group" data-grid-size="5" data-element="main"><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="YYKNR87"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="footer-top-item icon-sprite-before top-item-free"&gt;
    &lt;div class="text"&gt;
        &lt;h3&gt;{{trans "Free Delivery Service"}}&lt;/h3&gt;
        &lt;p&gt;{{trans "When Purchasing $1,000 Free Home Delivery Service"}}&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="GXJHG2C"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="footer-top-item icon-sprite-before top-item-satis"&gt;
    &lt;div class="text"&gt;
        &lt;h3&gt;{{trans "Satisfaction Guarantee"}}&lt;/h3&gt;
        &lt;p&gt;{{trans "Product Warranty Can Be Returned Within 7 Days"}}&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="OUMISHA"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="footer-top-item icon-sprite-before top-item-express"&gt;
    &lt;div class="text"&gt;
        &lt;h3&gt;{{trans "Express Delivery Service"}}&lt;/h3&gt;
        &lt;p&gt;{{trans "When Ordering Products By 12.00 Can Be Delivered Within The Day"}}&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="IEAM0YQ"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="footer-top-item icon-sprite-before top-item-payment"&gt;
    &lt;div class="text"&gt;
        &lt;h3&gt;{{trans "Various Payments"}}&lt;/h3&gt;
        &lt;p&gt;{{trans "Supports Multiple Payments Channel For Convenience"}}&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</div></div><div class="pagebuilder-column" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="OKTV66C"><div data-content-type="html" data-appearance="default" data-element="main">&lt;div class="footer-top-item icon-sprite-before top-item-delivery"&gt;
    &lt;div class="text"&gt;
        &lt;h3&gt;{{trans "Check Delivery Status"}}&lt;/h3&gt;
        &lt;p&gt;{{trans "Can Check The Status Easy Delivery"}}&lt;/p&gt;
    &lt;/div&gt;
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
