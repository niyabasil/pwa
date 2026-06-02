<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Block;

use Amasty\PromoBanners\Model\Banner\Data;
use Amasty\PromoBanners\Model\Rule;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Container extends Template
{
    /**
     * @var Data
     */
    private $bannerData;

    public function __construct(
        Data $bannerData,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->bannerData = $bannerData;
    }

    public function isVisible(): bool
    {
        return $this->getPosition() != Rule::POS_AMONG_PRODUCTS;
    }

    public function setPageBannerId(string $bannerId): void
    {
        $this->bannerData->pageBannerIds[] = $bannerId;
    }
}
