<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Block\Banner;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class Loader extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->registry = $registry;

        parent::__construct($context, $data);
    }

    public function getCurrentCategoryId(): ?int
    {
        $category = $this->registry->registry('current_category');

        return $category ? (int)$category->getId() : null;
    }

    public function getCurrentProductId(): ?int
    {
        $product = $this->registry->registry('current_product');

        return $product ? (int)$product->getId() : null;
    }
}
