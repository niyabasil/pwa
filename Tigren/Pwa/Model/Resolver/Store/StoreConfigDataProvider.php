<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Store;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * StoreConfig field data provider, used for GraphQL request processing.
 */
class StoreConfigDataProvider
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * StoreConfigDataProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get store config data
     *
     * @param StoreInterface $store
     * @param $requestConfigs
     * @return array
     */
    public function getStoreConfigData(StoreInterface $store, $requestConfigs): array
    {
        $storeConfigData = [];

        foreach ($requestConfigs as $path) {
            $requestPath = str_replace('__', '/', $path);
            $storeConfigData[] = [
                'path' => $path,
                'value' => $this->scopeConfig->getValue(
                    $requestPath,
                    ScopeInterface::SCOPE_STORE,
                    (int)$store->getId()
                )
            ];
        }

        return $storeConfigData;
    }
}
