<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model;

use Tigren\PushNotifications\Api\Data;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Tigren\PushNotifications\Api\CampaignStoreRepositoryInterface;
use Tigren\PushNotifications\Model\ResourceModel\CampaignStore as CampaignStoreResource;
use Tigren\PushNotifications\Model\CampaignStoreFactory;

/**
 * Class CampaignStoreRepository
 * @package Tigren\PushNotifications\Model
 */
class CampaignStoreRepository implements CampaignStoreRepositoryInterface
{
    /**
     * @var CampaignStoreResource
     */
    protected $resource;

    /**
     * @var \Tigren\PushNotifications\Model\CampaignStoreFactory
     */
    protected $factory;

    /**
     * CampaignStoreRepository constructor.
     *
     * @param CampaignStoreResource $resource
     * @param \Tigren\PushNotifications\Model\CampaignStoreFactory $factory
     */
    public function __construct(
        CampaignStoreResource $resource,
        CampaignStoreFactory $factory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
    }

    /**
     * @param Data\CampaignStoreInterface $item
     *
     * @return Data\CampaignStoreInterface
     *
     * @throws CouldNotSaveException
     */
    public function save(Data\CampaignStoreInterface $item)
    {
        try {
            $this->resource->save($item);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $item;
    }

    /**
     * @param $id
     *
     * @return Data\CampaignStoreInterface
     *
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $model = $this->factory->create();
        $this->resource->load($model, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Campaign Store with id "%1" does not exist.', $id));
        }

        return $model;
    }

    /**
     * @param Data\CampaignStoreInterface $item
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     */
    public function delete(Data\CampaignStoreInterface $item)
    {
        try {
            $this->resource->delete($item);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the campaign store: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool
     *
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
