<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Builder;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\Builder\LogoUrlBuilder;
use Tigren\PushNotifications\Model\Builder\UrlBuilder;

/**
 * Class NotificationBuilder
 * @package Tigren\PushNotifications\Model\Builder
 */
class NotificationBuilder implements BuilderInterface
{
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var \Tigren\PushNotifications\Model\Builder\LogoUrlBuilder
     */
    private $logoUrlBuilder;

    public function __construct(
        UrlBuilder $urlBuilder,
        LogoUrlBuilder $logoUrlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->logoUrlBuilder = $logoUrlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function build(array $params)
    {
        if ($params) {
            return $this->prepareNotificationBodyFromCampaignData($params);
        }

        throw new NotificationException(__('Campaign data not found'));
    }

    /**
     * @param array $campaignData
     *
     * @return array
     * @throws NotificationException
     */
    private function prepareNotificationBodyFromCampaignData(array $campaignData)
    {
        $body = [
            'title' => $campaignData[CampaignInterface::MESSAGE_TITLE],
            'body' => $campaignData[CampaignInterface::MESSAGE_BODY],
            'image' => $this->getNotificationLogo($campaignData),
        ];

        return $body;
    }

    /**
     * @param array $campaignData
     * @return array[]|string[]
     * @throws NotificationException
     */
    public function prepareNotificationDataFromCampaignData(array $campaignData)
    {
        $data = [
            'click_action' => $this->getNotificationLink($campaignData)
        ];

        return $data;
    }

    /**
     * @param array $campaignData
     *
     * @return string
     * @throws NotificationException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getNotificationLogo(array $campaignData)
    {
        return $this->logoUrlBuilder->build($campaignData);
    }

    /**
     * @param array $campaignData
     *
     * @return array|string
     * @throws NotificationException
     */
    private function getNotificationLink(array $campaignData)
    {
        $link = '#';

        /**TODO add new feature with checkbox and notification context button */
        //        if ($campaignData[CampaignInterface::BUTTON_NOTIFICATION_ENABLE]) {
        $link = $this->urlBuilder->build($campaignData);
        //        }

        return $link;
    }
}
