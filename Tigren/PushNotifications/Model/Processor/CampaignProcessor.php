<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Processor;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\Builder\DateTimeBuilder;
use Tigren\PushNotifications\Model\Campaign;
use Tigren\PushNotifications\Model\CampaignCustomerGroup;
use Tigren\PushNotifications\Model\CampaignRepository;
use Tigren\PushNotifications\Model\CustomerSegmentsValidator;
use Tigren\PushNotifications\Model\ResourceModel\Subscriber\Collection;
use Tigren\PushNotifications\Model\SubscriberRepository;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Tigren\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Tigren\PushNotifications\Model\OptionSource\Campaign\SegmentationSource;
use Tigren\PushNotifications\Ui\Component\Listing\Column\StoreOptions;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;

/**
 * Class CampaignProcessor
 * @package Tigren\PushNotifications\Model\Processor
 */
class CampaignProcessor
{
    /**
     * @var NotificationProcessor
     */
    private $notificationProcessor;

    /**
     * @var DateTimeBuilder
     */
    private $dateTimeBuilder;

    /**
     * @var CampaignCollectionFactory
     */
    private $campaignCollectionFactory;

    /**
     * @var SubscriberCollectionFactory
     */
    private $subscriberCollectionFactory;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * @var SubscriberRepository
     */
    private $subscriberRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerSegmentsValidator
     */
    private $customerSegmentsValidator;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        NotificationProcessor $notificationProcessor,
        DateTimeBuilder $dateTimeBuilder,
        CampaignCollectionFactory $campaignCollectionFactory,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        CampaignRepository $campaignRepository,
        SubscriberRepository $subscriberRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerSegmentsValidator $customerSegmentsValidator,
        Manager $moduleManager
    ) {
        $this->notificationProcessor = $notificationProcessor;
        $this->dateTimeBuilder = $dateTimeBuilder;
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->campaignRepository = $campaignRepository;
        $this->subscriberRepository = $subscriberRepository;
        $this->customerRepository = $customerRepository;
        $this->customerSegmentsValidator = $customerSegmentsValidator;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     * @param array $params
     * @return array
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws NotificationException
     */
    public function process(array $params)
    {
        $campaigns = $this->getValidCampaigns();
        $subscriberTokens = $this->getValidSubscribers();
        $segmentsModuleEnabled = $this->moduleManager->isEnabled('Tigren_Segments');

        if ($campaigns && $subscriberTokens) {
            /** @var Campaign $campaign */
            foreach ($campaigns as $campaign) {
                $stores = $campaign->getStoreIds();
                $segmentationSource = $campaign->getSegmentationSource();
                $counts = ['notificationCount' => 0, 'successNotificationCount' => 0];
                foreach ($subscriberTokens as $subscriberStore => $subscriberToken) {
                    if (array_search(StoreOptions::ALL_STORE_VIEWS, $stores) !== false
                        || array_search($subscriberStore, $stores) !== false
                    ) {
                        $segmentationSource == SegmentationSource::CUSTOMER_GROUPS || !$segmentsModuleEnabled
                            ? $this->validateCustomerGroups($subscriberToken, $campaign->getCustomerGroups())
                            : $this->customerSegmentsValidator->validateSegments(
                            $subscriberToken,
                            $campaign->getSegments()
                        );

                        if (empty($subscriberToken)) {
                            continue;
                        }
                        $result = $this->processCampaign($campaign, array_values($subscriberToken), $subscriberStore);
                        $counts['notificationCount'] += $result['notificationCount'];
                        $counts['successNotificationCount'] += $result['successNotificationCount'];
                    }
                }
                $campaign->setSentCounter($campaign->getSentCounter() + $counts['notificationCount']);
                $campaign->setShownCounter($campaign->getShownCounter() + $counts['successNotificationCount']);
                $campaign->processCampaign();
            }
        }

        return $params;
    }

    /**
     * @param array $subscriberToken
     * @param CampaignCustomerGroup[] $customerGroups
     *
     * @throws NotificationException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function validateCustomerGroups(&$subscriberToken, $customerGroups)
    {
        if (empty($customerGroups)) {
            return;
        }
        $customerGroupIds = [];

        foreach ($customerGroups as $group) {
            $customerGroupIds[] = $group->getGroupId();
        }

        foreach ($subscriberToken as $key => $token) {
            $subscriber = $this->subscriberRepository->getByToken($token);
            $id = $subscriber ? (int)$subscriber->getCustomerId() : false;

            if ($id) {
                $customer = $this->customerRepository->getById($id);

                if (!in_array($customer->getGroupId(), $customerGroupIds)) {
                    unset($subscriberToken[$key]);
                }
            } elseif ($id === false || !in_array($id, $customerGroupIds)) {
                unset($subscriberToken[$key]);
            }
        }
    }

    /**
     * @param CampaignInterface $campaign
     * @param array $subscriberTokens
     * @param int|null $storeId
     *
     * @return array
     *
     * @throws NotificationException
     * @throws AlreadyExistsException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    private function processCampaign($campaign, $subscriberTokens, $storeId = null)
    {
        return $this->notificationProcessor->processByMultipleTokens(
            $campaign->getCampaignId(),
            $subscriberTokens,
            $storeId
        );
    }

    /**
     * @return \Tigren\PushNotifications\Model\ResourceModel\Campaign\Collection
     */
    private function getCampaignCollection()
    {
        return $this->campaignCollectionFactory->create();
    }

    /**
     * @return Collection
     */
    private function getSubscriberCollection()
    {
        return $this->subscriberCollectionFactory->create();
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    private function getValidCampaigns()
    {
        $campaignCollection = $this->getCampaignCollection();
        $campaignCollection->addTimeFilter($this->dateTimeBuilder->getCurrentFormatedTime())
            ->addFieldToSelect(CampaignInterface::CAMPAIGN_ID);
        $validCampaigns = [];

        foreach ($campaignCollection->getData() as $campaign) {
            $validCampaigns[] = $this->campaignRepository->getById($campaign[CampaignInterface::CAMPAIGN_ID]);
        }

        return $validCampaigns;
    }

    /**
     * @return array
     */
    private function getValidSubscribers()
    {
        $campaignCollection = $this->getSubscriberCollection();
        $campaignCollection->addActiveFilter();

        if ($campaignCollection->getSize()) {
            $campaignCollection->getTokensOrderedByStore();
            $subscribers = $campaignCollection->getData();
            $data = [];

            foreach ($subscribers as $subscriber) {
                $data[$subscriber['store_id']][] = $subscriber['token'];
            }

            return $data;
        }

        return [];
    }
}
