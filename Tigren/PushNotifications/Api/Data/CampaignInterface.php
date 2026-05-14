<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Api\Data;

use Tigren\PushNotifications\Model\CampaignCustomerGroup;
use Tigren\PushNotifications\Model\CampaignSegments;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 *
 */
interface CampaignInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const CAMPAIGN_ID = 'campaign_id';
    /**
     *
     */
    const NAME = 'name';
    /**
     *
     */
    const SCHEDULED = 'scheduled';
    /**
     *
     */
    const IS_ACTIVE = 'is_active';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const SENT_COUNTER = 'sent';
    /**
     *
     */
    const SHOWN_COUNTER = 'shown';
    /**
     *
     */
    const LOGO_PATH = 'logo_path';
    /**
     *
     */
    const IS_DEFAULT_LOGO = 'is_default_logo';
    /**
     *
     */
    const MESSAGE_TITLE = 'message_title';
    /**
     *
     */
    const MESSAGE_BODY = 'message_body';
    /**
     *
     */
    const BUTTON_NOTIFICATION_ENABLE = 'button_notification_enable';
    /**
     *
     */
    const BUTTON_NOTIFICATION_TEXT = 'button_notification_text';
    /**
     *
     */
    const BUTTON_NOTIFICATION_URL = 'button_notification_url';
    /**
     *
     */
    const CLICKED_COUNTER = 'clicked';
    /**
     *
     */
    const UTM_PARAMS = 'utm_params';
    /**
     *
     */
    const CREATED_AT = 'created_at';
    /**
     *
     */
    const UPDATED_AT = 'updated_at';
    /**
     *
     */
    const STORES = 'stores';
    /**
     *
     */
    const STORE_ID = 'store_id';
    /**
     *
     */
    const SEGMENTATION_SOURCE = 'segmentation_source';
    /**
     *
     */
    const CUSTOMER_GROUPS = 'customer_groups';
    /**
     *
     */
    const CUSTOMER_SEGMENTS = 'customer_segments';
    /**#@-*/

    /**
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $campaignId
     *
     * @return CampaignInterface
     */
    public function setCampaignId($campaignId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return CampaignInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getScheduled();

    /**
     * @param string $scheduled
     *
     * @return CampaignInterface
     */
    public function setScheduled($scheduled);

    /**
     * @return int|string
     */
    public function getIsActive();

    /**
     * @param int|string $active
     *
     * @return CampaignInterface
     */
    public function setIsActive($active);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return CampaignInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getSentCounter();

    /**
     * @param int|string $sentCounter
     *
     * @return CampaignInterface
     */
    public function setSentCounter($sentCounter);

    /**
     * @return int
     */
    public function getShownCounter();

    /**
     * @param int|string $shownCounter
     *
     * @return CampaignInterface
     */
    public function setShownCounter($shownCounter);

    /**
     * @return int
     */
    public function getClickedCounter();

    /**
     * @param int|string $clickedCounter
     *
     * @return CampaignInterface
     */
    public function setClickedCounter($clickedCounter);

    /**
     * @return string
     */
    public function getLogoPath();

    /**
     * @param string $logoPath
     *
     * @return CampaignInterface
     */
    public function setLogoPath($logoPath);

    /**
     * @return int
     */
    public function getIsDefaultLogo();

    /**
     * @param int|string $isDefaultLogo
     *
     * @return CampaignInterface
     */
    public function setIsDefaultLogo($isDefaultLogo);

    /**
     * @return string
     */
    public function getMessageTitle();

    /**
     * @param string $messageTitle
     *
     * @return CampaignInterface
     */
    public function setMessageTitle($messageTitle);

    /**
     * @return string
     */
    public function getMessageBody();

    /**
     * @param string $messageBody
     *
     * @return CampaignInterface
     */
    public function setMessageBody($messageBody);

    /**
     * @return int
     */
    public function getButtonNotificationEnable();

    /**
     * @param int|string $buttonNotificationEnable
     *
     * @return CampaignInterface
     */
    public function setButtonNotificationEnable($buttonNotificationEnable);

    /**
     * @return string
     */
    public function getButtonNotificationText();

    /**
     * @param string $buttonNotificationText
     *
     * @return CampaignInterface
     */
    public function setButtonNotificationText($buttonNotificationText);

    /**
     * @return string
     */
    public function getButtonNotificationUrl();

    /**
     * @param string $buttonNotificationUrl
     *
     * @return CampaignInterface
     */
    public function setButtonNotificationUrl($buttonNotificationUrl);

    /**
     * @return string
     */
    public function getUtmParams();

    /**
     * @param string $utmParams
     *
     * @return CampaignInterface
     */
    public function setUtmParams($utmParams);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return CampaignInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $createdAt
     *
     * @return CampaignInterface
     */
    public function setUpdatedAt($createdAt);

    /**
     * @return int
     */
    public function getSegmentationSource();

    /**
     * @param int $source
     *
     * @return CampaignInterface
     */
    public function setSegmentationSource($source);

    /**
     * @return CampaignCustomerGroup[]
     */
    public function getCustomerGroups();

    /**
     * @param CampaignCustomerGroup[] $groups
     *
     * @return CampaignInterface
     */
    public function setCustomerGroups($groups);

    /**
     * @return CampaignSegments[]
     */
    public function getSegments();

    /**
     * @param CampaignSegments[] $segments
     *
     * @return CampaignInterface
     */
    public function setSegments($segments);

    /**
     * @return CampaignInterface
     *
     * @throws AlreadyExistsException
     */
    public function processCampaign();
}
