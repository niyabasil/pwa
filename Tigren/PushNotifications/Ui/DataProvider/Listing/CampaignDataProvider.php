<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\PushNotifications\Ui\DataProvider\Listing;

use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class CampaignDataProvider
 * @package Tigren\PushNotifications\Ui\DataProvider\Listing
 */
class CampaignDataProvider extends AbstractDataProvider
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param CampaignRepositoryInterface $repository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        CampaignRepositoryInterface $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->repository = $repository;
    }

    /**
     * Get data
     *
     * @return array
     *
     * @throws NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as $key => $campaign) {
            $campaign = $this->repository->getById($campaign['campaign_id']);
            $campaignData = $campaign->getData();
            $campaignData['store_id'] = $campaign->getStoreIds();
            $data['items'][$key] = $campaignData;
        }

        return $data;
    }
}
