<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\PwaPerformance\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class DisableSession
 * @package Tigren\PwaPerformance\Model\Config
 */
class DisableSession
{
    private const XML_PATH_GRAPHQL_DISABLED_SESSION = 'graphql/session/disabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get config value is session disabled for grapqhl area.
     *
     * @param string $scopeType
     * @param null|int|string $scopeCode
     * @return bool
     */
    public function isDisabled($scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null): bool
    {
        return (boolean)$this->scopeConfig->getValue(
            self::XML_PATH_GRAPHQL_DISABLED_SESSION,
            $scopeType,
            $scopeCode
        );
    }
}
