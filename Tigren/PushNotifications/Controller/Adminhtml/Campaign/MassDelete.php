<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Controller\Adminhtml\AbstractMassAction;
use Magento\Framework\Phrase;

/**
 * Class MassDelete
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @param $item
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    protected function itemAction($item)
    {
        $this->repository->deleteById($item->getCampaignId());
    }

    /**
     * @return Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}
