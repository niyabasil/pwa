<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Block\Banner;

use Magento\Catalog\Helper\Output;
use Magento\Framework\View\Element\Template;

class ProductListing extends Template
{
    /**
     * @var string
     */
    protected $_template = 'product_listing.phtml';

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    private $listProduct;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    private $formKey;

    /**
     * @var Output
     */
    private $catalogHelper;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Catalog\Helper\Output $catalogHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->listProduct = $listProduct;
        $this->formKey = $formKey;
        $this->catalogHelper = $catalogHelper;
    }

    public function getProductListingBlock()
    {
        return $this->listProduct;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function productAttribute($product, $attributeHtml, $attributeName)
    {
        return $this->catalogHelper->productAttribute($product, $attributeHtml, $attributeName);
    }
}
