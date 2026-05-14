<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\Backend\Controller\Adminhtml\Cache;

use Tigren\Pwa\Helper\Data as PwaHelper;

/**
 * Class FlushAll
 * @package Tigren\Pwa\Plugin\Magento\Backend\Controller\Adminhtml\Cache
 */
class FlushAll
{
    /**
     * @var PwaHelper
     */
    private $pwaHelper;

    /**
     * @param PwaHelper $pwaHelper
     */
    public function __construct(PwaHelper $pwaHelper)
    {
        $this->pwaHelper = $pwaHelper;
    }

    /**
     * @param \Magento\Backend\Controller\Adminhtml\Cache\FlushAll $subject
     * @return void
     */
    public function beforeExecute(
        \Magento\Backend\Controller\Adminhtml\Cache\FlushAll $subject
    ) {
        $this->pwaHelper->prepareGraphqlConfig();
    }

    /**
     * @param \Magento\Backend\Controller\Adminhtml\Cache\FlushAll $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Backend\Controller\Adminhtml\Cache\FlushAll $subject,
        $result
    ) {
        $this->pwaHelper->warmUpGraphqlConfigCache();
        return $result;
    }
}
