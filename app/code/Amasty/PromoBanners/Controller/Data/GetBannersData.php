<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Controller\Data;

use Amasty\PromoBanners\ViewModel\BannersDataProvider;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class GetBannersData extends Action
{
    /**
     * @var BannersDataProvider
     */
    private $dataProvider;

    public function __construct(
        Context $context,
        BannersDataProvider $dataProvider
    ) {
        parent::__construct($context);
        $this->dataProvider = $dataProvider;
    }

    public function execute(): ResultInterface
    {
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
            'bannersData' => $this->dataProvider->getBanners(),
        ]);
    }
}
