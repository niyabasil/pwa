<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 *
 */
interface CampaignStoreRepositoryInterface
{
    /**
     * Save
     *
     * @param Data\CampaignStoreInterface $item
     *
     * @return Data\CampaignStoreInterface
     */
    public function save(Data\CampaignStoreInterface $item);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return Data\CampaignStoreInterface
     *
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param Data\CampaignStoreInterface $item
     *
     * @return bool true on success
     *
     * @throws CouldNotDeleteException
     */
    public function delete(Data\CampaignStoreInterface $item);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     *
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);
}
