<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\PwaPerformance\Plugin\Magento\Framework\Session;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Tigren\PwaPerformance\Model\Config\DisableSession as DisableSessionConfig;

/**
 * Class SessionStartChecker
 * @package Tigren\PwaPerformance\Plugin\Magento\Framework\Session
 */
class SessionStartChecker
{
    /**
     * @var DisableSessionConfig
     */
    private $disableSessionConfig;

    /**
     * @var State
     */
    private $appState;

    /**
     * @param DisableSessionConfig $disableSessionConfig
     * @param State $appState
     */
    public function __construct(
        DisableSessionConfig $disableSessionConfig,
        State $appState
    ) {
        $this->disableSessionConfig = $disableSessionConfig;
        $this->appState = $appState;
    }

    /**
     * @param \Magento\Framework\Session\SessionStartChecker $subject
     * @param bool $result
     * @return bool
     */
    public function afterCheck(\Magento\Framework\Session\SessionStartChecker $subject, bool $result): bool
    {
        if (!$result) {
            return false;
        }
        try {
            if ($this->appState->getAreaCode() === Area::AREA_GRAPHQL && $this->disableSessionConfig->isDisabled()) {
                $result = false;
            }
        } catch (LocalizedException $e) {
        } finally { //@codingStandardsIgnoreLine
            return $result;
        }
    }
}
