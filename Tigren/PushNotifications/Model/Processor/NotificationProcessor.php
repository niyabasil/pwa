<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Processor;

use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\Api\Sender;
use Tigren\PushNotifications\Model\Builder\NotificationBuilder;
use Tigren\PushNotifications\Model\Builder\NotificationMessageBuilder;
use Tigren\PushNotifications\Model\ConfigProvider;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class NotificationProcessor
 * @package Tigren\PushNotifications\Model\Processor
 */
class NotificationProcessor implements ProcessorInterface
{
    /**
     * @var NotificationBuilder
     */
    private $notificationBuilder;

    /**
     * @var Sender
     */
    private $sender;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var NotificationMessageBuilder
     */
    private $notificationMessageBuilder;

    public function __construct(
        NotificationBuilder $notificationBuilder,
        Sender $sender,
        ConfigProvider $configProvider,
        CampaignRepositoryInterface $campaignRepository,
        NotificationMessageBuilder $notificationMessageBuilder
    ) {
        $this->notificationBuilder = $notificationBuilder;
        $this->sender = $sender;
        $this->configProvider = $configProvider;
        $this->campaignRepository = $campaignRepository;
        $this->notificationMessageBuilder = $notificationMessageBuilder;
    }

    /**
     * @param int $campaignId
     * @param string $token
     * @param bool $isTest
     *
     * @return array
     *
     * @throws NotificationException
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function processByToken($campaignId, $token, $isTest = false)
    {
        if ($campaignId && $token) {
            $notificationData = [
                'to' => $token,
                'notification' => $this->prepareNotificationBody($campaignId)
            ];

            if ($isTest) {
                unset($notificationData['notification']['click_counter_url']);
            }

            return $this->process($notificationData);
        }

        throw new NotificationException(__('Error in process Push Notification.'));
    }

    /**
     * @param int $campaignId
     * @param array $tokensArray
     * @param int|null $storeId
     *
     * @return array
     *
     * @throws NotificationException
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function processByMultipleTokens($campaignId, array $tokensArray, $storeId = null)
    {
        if ($campaignId && $tokensArray) {
            return $this->process(
                [
                    'registration_ids' => $tokensArray,
                    'notification' => $this->prepareNotificationBody($campaignId, $storeId),
                    'data' => $this->prepareNotificationData($campaignId)
                ],
                $storeId
            );
        }

        throw new NotificationException(__('Error in process Push Notification.'));
    }

    /**
     * @inheritdoc
     */
    public function process(array $params, $storeId = null)
    {
        $response = $this->sender->send($params, $storeId);
        $status = $this->getStatusByResponse($response);
        $notificationCount = $this->getNotificationCount($params);

        return [
            'status' => $status,
            'message' => $this->notificationMessageBuilder->build(
                [
                    'status' => $status,
                    'notification_count' => $notificationCount
                ]
            ),
            'notificationCount' => $notificationCount,
            'successNotificationCount' => $this->getSuccessNotificationsByResponse($response),
            'failureNotificationCount' => $this->getFailureNotificationsByResponse($response),
        ];
    }

    /**
     * @param array $params
     * @return int
     */
    private function getNotificationCount(array $params)
    {
        if (isset($params['to'])) {
            $notificationCount = 1;
        } else {
            $notificationCount = count($params['registration_ids']);
        }

        return $notificationCount;
    }

    /**
     * @param int $campaignId
     *
     * @return array|string
     *
     * @throws NotificationException
     * @throws NoSuchEntityException
     */
    private function prepareNotificationBody($campaignId)
    {
        $campaign = $this->campaignRepository->getById($campaignId);

        if ($campaign->getCampaignId()) {
            $body = $this->notificationBuilder->build($campaign->getData());
        } else {
            throw new NotificationException(__('Campaign with ID "%1" not found.', $campaignId));
        }

        return $body;
    }

    /**
     * @throws NoSuchEntityException
     * @throws NotificationException
     */
    private function prepareNotificationData($campaignId): array
    {
        $campaign = $this->campaignRepository->getById($campaignId);

        if ($campaign->getCampaignId()) {
            $body = $this->notificationBuilder->prepareNotificationDataFromCampaignData($campaign->getData());
        } else {
            throw new NotificationException(__('Campaign with ID "%1" not found.', $campaignId));
        }

        return $body;
    }

    /**
     * @param array $response
     * @return bool
     */
    private function getStatusByResponse(array $response)
    {
        return isset($response['success']) && $response['success'] == 1;
    }

    /**
     * @param array $response
     * @return bool
     */
    private function getSuccessNotificationsByResponse(array $response)
    {
        return $this->getSendDataFromResponse($response);
    }

    /**
     * @param array $response
     * @return bool
     */
    private function getFailureNotificationsByResponse(array $response)
    {
        return $this->getSendDataFromResponse($response, 'failure');
    }

    /**
     * @param array $response
     * @param string $status
     * @return int
     */
    private function getSendDataFromResponse($response, $status = 'success')
    {
        return isset($response[$status]) ? $response[$status] : 0;
    }
}
