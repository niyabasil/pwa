<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\Backend\Controller\Adminhtml\Cache;

use Tigren\Pwa\Helper\Data as PwaHelper;

/**
 * Class MassRefresh
 * @package Tigren\Pwa\Plugin\Magento\Backend\Controller\Adminhtml\Cache
 */
class MassRefresh
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
     * @param \Magento\Backend\Controller\Adminhtml\Cache\MassRefresh $subject
     * @return void
     */
    public function beforeExecute(
        \Magento\Backend\Controller\Adminhtml\Cache\MassRefresh $subject
    ) {
        $types = $subject->getRequest()->getParam('types');
        if (!is_array($types)) {
            $types = [];
        }

        if (in_array('config', $types)) {
            $this->pwaHelper->prepareGraphqlConfig();
        }
    }

    /**
     * @param \Magento\Backend\Controller\Adminhtml\Cache\MassRefresh $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Backend\Controller\Adminhtml\Cache\MassRefresh $subject,
        $result
    ) {
        $types = $subject->getRequest()->getParam('types');
        if (!is_array($types)) {
            $types = [];
        }

        if (in_array('config', $types)) {
            $this->pwaHelper->warmUpGraphqlConfigCache();
        }

        return $result;
    }
}
