<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\Framework\App\Cache;

use Tigren\Pwa\Helper\Data as PwaHelper;

/**
 * Class Manager
 * @package Tigren\Pwa\Plugin\Magento\Framework\App\Cache
 */
class Manager
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
     * @param \Magento\Framework\App\Cache\Manager $subject
     * @param $types
     */
    public function beforeFlush(
        \Magento\Framework\App\Cache\Manager $subject,
        $types
    ) {
        if (in_array('config', $types)) {
            $this->pwaHelper->prepareGraphqlConfig();
        }
    }

    /**
     * @param \Magento\Framework\App\Cache\Manager $subject
     * @param $result
     * @param $types
     * @return mixed
     */
    public function afterFlush(
        \Magento\Framework\App\Cache\Manager $subject,
        $result,
        $types
    ) {
        if (in_array('config', $types)) {
            $this->pwaHelper->warmUpGraphqlConfigCache();
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\App\Cache\Manager $subject
     * @param $types
     */
    public function beforeClean(
        \Magento\Framework\App\Cache\Manager $subject,
        $types
    ) {
        if (in_array('config', $types)) {
            $this->pwaHelper->prepareGraphqlConfig();
        }
    }

    /**
     * @param \Magento\Framework\App\Cache\Manager $subject
     * @param $result
     * @param $types
     * @return mixed
     */
    public function afterClean(
        \Magento\Framework\App\Cache\Manager $subject,
        $result,
        $types
    ) {
        if (in_array('config', $types)) {
            $this->pwaHelper->warmUpGraphqlConfigCache();
        }

        return $result;
    }
}
