<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model;

use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Tigren\PushNotifications\Api\Data\CampaignCustomerGroupInterface;
use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Api\Data\CampaignSegmentsInterface;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Active;
use Tigren\PushNotifications\Model\OptionSource\Campaign\SegmentationSource;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Status;
use Tigren\PushNotifications\Model\ResourceModel\Campaign as CampaignResource;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\Collection;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Tigren\PushNotifications\Model\ResourceModel\CampaignCustomerGroup\CollectionFactory
    as CampaignCustomerGroupCollectionFactory;
use Tigren\PushNotifications\Model\ResourceModel\CampaignSegments\CollectionFactory
    as CampaignSegmentsCollectionFactory;
use Exception;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

/**
 * Class CampaignRepository
 * @package Tigren\PushNotifications\Model
 */
class CampaignRepository implements CampaignRepositoryInterface
{
    /**
     * This field adds to the campaign with true value, when status change is not required before model save
     */
    const SKIP_STATUS_CHANGE = 'skip_status_change';

    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CampaignFactory
     */
    private $campaignFactory;

    /**
     * @var CampaignResource
     */
    private $campaignResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $campaign;

    /**
     * @var CollectionFactory
     */
    private $campaignCollectionFactory;

    /**
     * @var CampaignCustomerGroupFactory
     */
    private $campaignCustomerGroupFactory;

    /**
     * @var \Tigren\PushNotifications\Model\ResourceModel\CampaignCustomerGroup
     */
    private $campaignCustomerGroupResource;

    /**
     * @var CampaignCustomerGroupCollectionFactory
     */
    private $campaignCustomerGroupCollectionFactory;

    /**
     * @var CampaignSegmentsFactory
     */
    private $campaignSegmentsFactory;

    /**
     * @var \Tigren\PushNotifications\Model\ResourceModel\CampaignSegments
     */
    private $campaignSegmentsResource;

    /**
     * @var CampaignSegmentsCollectionFactory
     */
    private $campaignSegmentsCollectionFactory;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        CampaignFactory $campaignFactory,
        CampaignResource $campaignResource,
        CollectionFactory $campaignCollectionFactory,
        CampaignCustomerGroupFactory $campaignCustomerGroupFactory,
        \Tigren\PushNotifications\Model\ResourceModel\CampaignCustomerGroup $campaignCustomerGroupResource,
        CampaignCustomerGroupCollectionFactory $campaignCustomerGroupCollectionFactory,
        CampaignSegmentsFactory $campaignSegmentsFactory,
        \Tigren\PushNotifications\Model\ResourceModel\CampaignSegments $campaignSegmentsResource,
        CampaignSegmentsCollectionFactory $campaignSegmentsCollectionFactory,
        Manager $moduleManager
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->campaignFactory = $campaignFactory;
        $this->campaignResource = $campaignResource;
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->campaignCustomerGroupFactory = $campaignCustomerGroupFactory;
        $this->campaignCustomerGroupResource = $campaignCustomerGroupResource;
        $this->campaignCustomerGroupCollectionFactory = $campaignCustomerGroupCollectionFactory;
        $this->campaignSegmentsFactory = $campaignSegmentsFactory;
        $this->campaignSegmentsResource = $campaignSegmentsResource;
        $this->campaignSegmentsCollectionFactory = $campaignSegmentsCollectionFactory;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function save(CampaignInterface $campaign)
    {
        try {
            $campaign = $this->prepareCampaignForSave($campaign);

            $this->campaignResource->save($campaign);
            $campaign->getSegmentationSource() === SegmentationSource::CUSTOMER_GROUPS
                ? $this->saveCustomerGroups($campaign)
                : $this->saveSegments($campaign);
            unset($this->campaign[$campaign->getCampaignId()]);
        } catch (Exception $e) {
            if ($campaign->getCampaignId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save campaign with ID %1. Error: %2',
                        [$campaign->getCampaignId(), $e->getMessage()]
                    )
                );
            }

            throw new CouldNotSaveException(__('Unable to save new campaign. Error: %1', $e->getMessage()));
        }

        return $campaign;
    }

    /**
     * @param CampaignInterface $campaign
     *
     * @throws AlreadyExistsException
     */
    private function saveCustomerGroups($campaign)
    {
        /** @var ResourceModel\CampaignCustomerGroup\Collection $campaignCustomerGroupCollection */
        $campaignCustomerGroupCollection = $this->campaignCustomerGroupCollectionFactory->create();
        $campaignCustomerGroupCollection->addFieldToFilter(
            CampaignCustomerGroupInterface::CAMPAIGN_ID,
            $campaign->getCampaignId()
        );
        $campaignCustomerGroupCollection->walk('delete');

        if ($groups = $campaign->getCustomerGroups()) {
            foreach ($groups as $group) {
                $group->unsetData(CampaignCustomerGroupInterface::CAMPAIGN_GROUP_ID);
                $group->setCampaignId($campaign->getCampaignId());
                $this->campaignCustomerGroupResource->save($group);
            }
        }
    }

    /**
     * @param CampaignInterface $campaign
     *
     * @throws AlreadyExistsException
     */
    private function saveSegments($campaign)
    {
        /** @var ResourceModel\CampaignSegments\Collection $campaignSegmentsCollection */
        $campaignSegmentsCollection = $this->campaignSegmentsCollectionFactory->create();
        $campaignSegmentsCollection->addFieldToFilter(
            CampaignSegmentsInterface::CAMPAIGN_ID,
            $campaign->getCampaignId()
        );
        $campaignSegmentsCollection->walk('delete');

        if ($segments = $campaign->getSegments()) {
            foreach ($segments as $segment) {
                $segment->unsetData(CampaignSegmentsInterface::CAMPAIGN_SEGMENT_ID);
                $segment->setCampaignId($campaign->getCampaignId());
                $this->campaignSegmentsResource->save($segment);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getById($campaignId)
    {
        if (!isset($this->campaign[$campaignId])) {

            /** @var Campaign $campaign */
            $campaign = $this->campaignFactory->create();
            $this->campaignResource->load($campaign, $campaignId);

            if (!$campaign->getCampaignId()) {
                throw new NoSuchEntityException(__('Campaign with specified ID "%1" not found.', $campaignId));
            }

            /** @var ResourceModel\CampaignCustomerGroup\Collection $campaignCustomerGroupCollection */
            $campaignCustomerGroupCollection = $this->campaignCustomerGroupCollectionFactory->create();
            $campaignCustomerGroupCollection->addFieldToFilter(
                CampaignCustomerGroupInterface::CAMPAIGN_ID,
                $campaign->getCampaignId()
            );
            $campaign->setCustomerGroups($campaignCustomerGroupCollection->getItems());

            if ($this->moduleManager->isOutputEnabled('Tigren_Segments')) {
                /** @var ResourceModel\CampaignSegments\Collection $campaignSegmentsCollection */
                $campaignSegmentsCollection = $this->campaignSegmentsCollectionFactory->create();
                $campaignSegmentsCollection->addFieldToFilter(
                    CampaignSegmentsInterface::CAMPAIGN_ID,
                    $campaign->getCampaignId()
                );
                $campaign->setSegments($campaignSegmentsCollection->getItems());
            }

            $this->campaign[$campaignId] = $campaign;
        }

        return $this->campaign[$campaignId];
    }

    /**
     * @param int $campaignId
     *
     * @return $this
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function increaseClickCounter($campaignId)
    {
        $campaign = $this->getById($campaignId);
        $campaign->setClickedCounter((int)$campaign->getClickedCounter() + 1);
        $campaign->setData('skip_status_change', true);

        $this->save($campaign);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function delete(CampaignInterface $campaign)
    {
        try {
            $this->campaignResource->delete($campaign);
            unset($this->campaign[$campaign->getCampaignId()]);
        } catch (Exception $e) {
            if ($campaign->getCampaignId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove question with ID %1. Error: %2',
                        [$campaign->getCampaignId(), $e->getMessage()]
                    )
                );
            }

            throw new CouldNotDeleteException(__('Unable to remove campaign. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     * @param $campaignId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($campaignId)
    {
        $campaignModel = $this->getById($campaignId);
        $this->delete($campaignModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        /** @var Collection $campaignCollection */
        $campaignCollection = $this->campaignCollectionFactory->create();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $campaignCollection);
        }

        $searchResults->setTotalCount($campaignCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $campaignCollection);
        }

        $campaignCollection->setCurPage($searchCriteria->getCurrentPage());
        $campaignCollection->setPageSize($searchCriteria->getPageSize());
        $campaign = [];

        /** @var CampaignInterface $campaign */
        foreach ($campaignCollection->getItems() as $campaign) {
            $campaign[] = $this->getById($campaign->getId());
        }

        $searchResults->setItems($campaign);

        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getEmptyCampaignCustomerGroupModel()
    {
        return $this->campaignCustomerGroupFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getEmptyCampaignSegmentModel()
    {
        return $this->campaignSegmentsFactory->create();
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroupsByCampaignId($id)
    {
        /** @var ResourceModel\CampaignCustomerGroup\Collection $collection */
        $collection = $this->campaignCustomerGroupCollectionFactory->create();
        $collection->addFieldToFilter(CampaignCustomerGroupInterface::CAMPAIGN_ID, $id);

        return $collection->getData();
    }

    /**
     * @inheritdoc
     */
    public function getSegmentsByCampaignId($id)
    {
        /** @var ResourceModel\CampaignSegments\Collection $collection */
        $collection = $this->campaignSegmentsCollectionFactory->create();
        $collection->addFieldToFilter(CampaignSegmentsInterface::CAMPAIGN_ID, $id);

        return $collection->getData();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $campaignCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $campaignCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $campaignCollection->addFieldToFilter(
                $filter->getField(),
                [
                    $condition => $filter->getValue()
                ]
            );
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection $campaignCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $campaignCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $campaignCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? 'DESC' : 'ASC'
            );
        }
    }

    /**
     * @param CampaignInterface $campaign
     *
     * @return CampaignInterface
     *
     * @throws NoSuchEntityException
     */
    private function prepareCampaignForSave(CampaignInterface $campaign)
    {
        if ($campaign->getCampaignId()) {
            $savedCampaign = $this->getById($campaign->getCampaignId());
            $this->setCorrectStatus($campaign);
            $savedCampaign->addData($campaign->getData());

            return $savedCampaign;
        } else {
            $campaign->setStatus(Status::STATUS_SCHEDULED);
        }

        return $campaign;
    }

    /**
     * @param CampaignInterface $savedCampaign
     * @param CampaignInterface $campaign
     */
    private function setCorrectStatus($campaign)
    {
        if ($campaign->getStatus() != Status::STATUS_SCHEDULED
            && !$campaign->getData(self::SKIP_STATUS_CHANGE)
        ) {
            $status = $campaign->getIsActive() == Active::STATUS_INACTIVE
                ? Status::STATUS_EDITED
                : Status::STATUS_SCHEDULED;
            $campaign->setStatus($status);
        }
    }
}
