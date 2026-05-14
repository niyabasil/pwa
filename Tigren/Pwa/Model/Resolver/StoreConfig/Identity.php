<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Model\Resolver\StoreConfig;

use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

/**
 * Class Identity
 * @package Tigren\Pwa\Model\Resolver\StoreConfig
 */
class Identity implements IdentityInterface
{
    /**
     * @var string
     */
    private $cacheTag = 'pwa_store_configuration';

    /**
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        return [$this->cacheTag];
    }
}
