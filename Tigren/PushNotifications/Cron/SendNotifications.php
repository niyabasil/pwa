<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Cron;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\ConfigProvider;
use Tigren\PushNotifications\Model\Processor\CampaignProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class SendNotifications
 * @package Tigren\PushNotifications\Cron
 */
class SendNotifications
{
    /**
     * @var CampaignProcessor
     */
    private $campaignProcessor;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @param CampaignProcessor $campaignProcessor
     * @param LoggerInterface $logger
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        CampaignProcessor $campaignProcessor,
        LoggerInterface $logger,
        ConfigProvider $configProvider
    ) {
        $this->campaignProcessor = $campaignProcessor;
        $this->logger = $logger;
        $this->configProvider = $configProvider;
    }

    /**
     * @return $this
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    public function execute()
    {
        try {
            if (!$this->configProvider->isModuleEnable()) {
                throw new NotificationException(__('Module is disabled'));
            }

            $this->campaignProcessor->process([]);
        } catch (NotificationException $exception) {
            $this->logger->critical($exception);
        }

        return $this;
    }
}
