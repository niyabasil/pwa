<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\PushNotifications\Ui\DataProvider\Form;

use Tigren\PushNotifications\Api\Data\CampaignCustomerGroupInterface;
use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Api\Data\CampaignSegmentsInterface;
use Tigren\PushNotifications\Model\Campaign;
use Tigren\PushNotifications\Model\FileUploader\FileInfoCollector;
use Tigren\PushNotifications\Model\OptionSource\Campaign\SegmentationSource;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class CampaignDataProvider
 * @package Tigren\PushNotifications\Ui\DataProvider\Form
 */
class CampaignDataProvider extends AbstractDataProvider
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var FileInfoCollector
     */
    private $fileInfoCollector;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param CampaignRepositoryInterface $repository
     * @param DataPersistorInterface $dataPersistor
     * @param FileInfoCollector $fileInfoCollector
     * @param Manager $moduleManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        CampaignRepositoryInterface $repository,
        DataPersistorInterface $dataPersistor,
        FileInfoCollector $fileInfoCollector,
        Manager $moduleManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->repository = $repository;
        $this->dataPersistor = $dataPersistor;
        $this->fileInfoCollector = $fileInfoCollector;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Get data
     *
     * @return array
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getData()
    {
        $data = parent::getData();

        /**
         * It is need for support of several fieldsets.
         * For details @see \Magento\Ui\Component\Form::getDataSourceData
         */
        if ($data['totalRecords'] > 0) {
            $campaignId = (int)$data['items'][0][CampaignInterface::CAMPAIGN_ID];
            /** @var Campaign $campaignModel */
            $campaignModel = $this->repository->getById($campaignId);
            $campaignData = $campaignModel->getData();
            $data[$campaignId] = $campaignData;
            $data[$campaignId][CampaignInterface::LOGO_PATH] = $campaignModel->getIsDefaultLogo()
                ? []
                : $this->getLogoData($campaignModel->getLogoPath());
            $stores = [];
            foreach ($campaignModel->getStores() as $store) {
                $stores[] = $store->getStoreId();
            }
            $data[$campaignId]['storeviews'] = implode(',', $stores);

            $campaignGroups = [];
            $customerGroups = $this->repository->getCustomerGroupsByCampaignId($campaignId);

            foreach ($customerGroups as $customerGroup) {
                $campaignGroups[] = $customerGroup[CampaignCustomerGroupInterface::GROUP_ID];
            }
            $data[$campaignId][CampaignInterface::CUSTOMER_GROUPS] = $campaignGroups;

            if ($this->moduleManager->isEnabled('Tigren_Segments')) {
                $campaignSegments = [];
                $segments = $this->repository->getSegmentsByCampaignId($campaignId);

                foreach ($segments as $segment) {
                    $campaignSegments[] = $segment[CampaignSegmentsInterface::SEGMENT_ID];
                }
                $data[$campaignId][CampaignInterface::CUSTOMER_SEGMENTS] = $campaignSegments;
            } else {
                $data[$campaignId][CampaignInterface::SEGMENTATION_SOURCE] = SegmentationSource::CUSTOMER_GROUPS;
            }
        }

        if ($savedData = $this->dataPersistor->get('campaignData')) {
            $savedCampaignId = isset($savedData['campaign_id']) ? $savedData['campaign_id'] : null;
            if (isset($data[$savedCampaignId])) {
                $data[$savedCampaignId] = array_merge($data[$savedCampaignId], $savedData);
            } else {
                $data[$savedCampaignId] = $savedData;
            }
            $stores = [];
            foreach ($data[$savedCampaignId]->getStores() as $store) {
                $stores[] = $store->getStoreId();
            }
            $data[$savedCampaignId]['storeviews'] = implode($stores);

            $this->dataPersistor->clear('campaignData');
        }

        return $data;
    }

    /**
     * @param $filePath
     * @return array|null
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getLogoData($filePath)
    {
        return $this->fileInfoCollector->getInfoByFilePath($filePath);
    }
}
