<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Controller\Adminhtml\AbstractMassAction;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Active;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Phrase;

/**
 * Class MassStatus
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class MassStatus extends AbstractMassAction
{
    /**
     * @param $item
     *
     * @throws CouldNotSaveException
     */
    protected function itemAction($item)
    {
        $status = $this->getStatus();

        $item->setIsActive($status);
        $this->repository->save($item);
    }

    /**
     * @return int
     */
    private function getStatus()
    {
        return $this->getRequest()->getParam('status') == 'activate'
            ? Active::STATUS_ACTIVE : Active::STATUS_INACTIVE;
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
        if ($this->getStatus() == Active::STATUS_ACTIVE) {
            return $this->getActivateMessage($collectionSize);
        } else {
            return $this->getDeactivateMessage($collectionSize);
        }
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    private function getActivateMessage($collectionSize)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been activated.', $collectionSize);
        }

        return __('No records have been activated.');
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    private function getDeactivateMessage($collectionSize)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deactivated.', $collectionSize);
        }

        return __('No records have been deactivated.');
    }
}
