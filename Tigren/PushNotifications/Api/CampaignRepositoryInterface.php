<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Api;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Model\CampaignCustomerGroup;
use Tigren\PushNotifications\Model\CampaignSegments;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
interface CampaignRepositoryInterface
{
    /**
     * Save
     *
     * @param CampaignInterface $campaign
     * @return CampaignInterface
     * @throws CouldNotSaveException
     */
    public function save(CampaignInterface $campaign);

    /**
     * Get by id
     *
     * @param int $campaignId
     * @return CampaignInterface
     * @throws NoSuchEntityException
     */
    public function getById($campaignId);

    /**
     *
     * @param int $campaignId
     * @return $this
     */
    public function increaseClickCounter($campaignId);

    /**
     * Delete
     *
     * @param CampaignInterface $campaign
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function delete(CampaignInterface $campaign);

    /**
     * Delete by id
     *
     * @param int $campaignId
     * @return bool true on success
     * @throws CouldNotDeleteException
     */
    public function deleteById($campaignId);

    /**
     * Lists
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @return CampaignCustomerGroup
     */
    public function getEmptyCampaignCustomerGroupModel();

    /**
     * @return CampaignSegments
     */
    public function getEmptyCampaignSegmentModel();

    /**
     * @param int $id
     *
     * @return array
     */
    public function getCustomerGroupsByCampaignId($id);

    /**
     * @param int $id
     *
     * @return array
     */
    public function getSegmentsByCampaignId($id);
}
