<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Builder;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Model\ConfigProvider;
use Tigren\PushNotifications\Model\FileUploader\FileInfoCollector;

/**
 * Class LogoUrlBuilder
 * @package Tigren\PushNotifications\Model\Builder
 */
class LogoUrlBuilder implements BuilderInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FileInfoCollector
     */
    private $fileInfoCollector;

    public function __construct(
        ConfigProvider $configProvider,
        FileInfoCollector $fileInfoCollector
    ) {
        $this->configProvider = $configProvider;
        $this->fileInfoCollector = $fileInfoCollector;
    }

    /**
     * @inheritdoc
     * @param array $params
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function build(array $params)
    {
        if ($params[CampaignInterface::IS_DEFAULT_LOGO]) {
            return $this->getDefaultLogoUrl();
        }

        return $this->getUrlFromFileInfo(
            $this->fileInfoCollector->getInfoByFilePath($params[CampaignInterface::LOGO_PATH])
        );
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getDefaultLogoUrl()
    {
        $url = '';

        if ($logoPath = $this->configProvider->getLogoPath()) {
            $url = $this->getUrlFromFileInfo(
                $this->fileInfoCollector->getInfoByFilePath(DIRECTORY_SEPARATOR . $logoPath)
            );
        }

        return $url;
    }

    /**
     * @param $fileInfo
     * @return string
     */
    private function getUrlFromFileInfo($fileInfo)
    {
        if ($fileInfo) {
            $fileInfo = array_shift($fileInfo);
        }

        return isset($fileInfo['url']) ? $fileInfo['url'] : '';
    }
}
