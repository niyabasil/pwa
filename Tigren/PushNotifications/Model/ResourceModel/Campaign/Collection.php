<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel\Campaign;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Active;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Status;
use Tigren\PushNotifications\Model\ResourceModel\Campaign;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zend_DB_Select;

/**
 * @method \Tigren\PushNotifications\Model\Campaign[] getItems()
 * @method Campaign getResource()
 */
class Collection extends AbstractCollection
{
    /**
     *
     */
    const DASHBOARD_CAMPAIGNS_GRID_LIMIT = 5;

    /**
     *
     */
    const DASHBOARD_CAMPAIGNS_SCHEDULED_ITEMS_GRID_LIMIT = 2;

    /**
     * cache tag
     */
    const CACHE_TAG = 'tigren_notifications_campaign';

    /**
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @throws LocalizedException
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init(
            \Tigren\PushNotifications\Model\Campaign::class,
            Campaign::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG];
    }

    /**
     * @param $currentExecution
     *
     * @return $this
     */
    public function addTimeFilter($currentExecution)
    {
        $this->addFieldToFilter(
            'main_table.' . CampaignInterface::SCHEDULED,
            [
                'lt' => $currentExecution
            ]
        );

        $this->addFilterByStatus();
        $this->getSelect()
            ->where(
                'main_table.' . CampaignInterface::IS_ACTIVE . ' = ?',
                Active::STATUS_ACTIVE
            );

        return $this;
    }

    /**
     * @param int $status
     *
     * @return Collection
     */
    public function addFilterByStatus($status = Status::STATUS_SCHEDULED)
    {
        $this->getSelect()
            ->where(
                'main_table.' . CampaignInterface::STATUS . ' = ?',
                $status
            );

        return $this;
    }

    /**
     *
     * @param string $direction
     *
     * @return $this
     */
    public function orderByScheduled($direction = self::SORT_ORDER_DESC)
    {
        $this->setOrder(CampaignInterface::SCHEDULED, $direction);

        return $this;
    }

    /**
     * @return $this
     */
    public function addFieldToSelectDashboardGrid()
    {
        $this->addFieldToSelect(
            [
                CampaignInterface::CAMPAIGN_ID,
                CampaignInterface::NAME,
                CampaignInterface::STATUS,
                CampaignInterface::SHOWN_COUNTER,
                CampaignInterface::CLICKED_COUNTER,
                CampaignInterface::SCHEDULED,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function dashboardGridFilter()
    {
        $scheduledItems = $this->getScheduledItems();
        $passedItems = $this->getPassedItems();
        $allItems = array_merge($passedItems, $scheduledItems);
        $allItems = array_slice(array_reverse($allItems), 0, self::DASHBOARD_CAMPAIGNS_GRID_LIMIT);

        $this->addFieldToFilter(CampaignInterface::CAMPAIGN_ID, ['in' => $allItems])
            ->orderByScheduled(self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @param int $limit
     * @param int $status
     *
     * @return array
     */
    private function getItemsByStatusAndLimit($limit, $status = Status::STATUS_SCHEDULED)
    {
        $collection = clone $this;
        $collection->getSelect()->reset(Zend_DB_Select::COLUMNS);
        $collection->addFieldToSelect(CampaignInterface::CAMPAIGN_ID);
        $collection->addFilterByStatus($status);
        $collection->orderByScheduled(self::SORT_ORDER_ASC);
        $collection->setPageSize($limit);

        return array_keys($collection->getItems());
    }

    /**
     * @return array
     */
    private function getScheduledItems()
    {
        return $this->getItemsByStatusAndLimit(self::DASHBOARD_CAMPAIGNS_SCHEDULED_ITEMS_GRID_LIMIT);
    }

    /**
     * @return array
     */
    private function getPassedItems()
    {
        return $this->getItemsByStatusAndLimit(self::DASHBOARD_CAMPAIGNS_GRID_LIMIT, Status::STATUS_PASSED);
    }

    /**
     * @return int
     */
    public function getClicksTotal()
    {
        $result = 0;
        $clickedItems = $this->addFilterByStatus(Status::STATUS_PASSED)
            ->addFieldToSelect(CampaignInterface::CLICKED_COUNTER)
            ->getData();

        if ($clickedItems) {
            foreach ($clickedItems as $clickedItem) {
                $result += $clickedItem[CampaignInterface::CLICKED_COUNTER];
            }
        }

        return $result;
    }

    /**
     * Retrieve collection second item
     *
     * @return DataObject
     */
    public function getSecondItem()
    {
        $this->load();

        if (count($this->_items)) {
            reset($this->_items);

            return current(array_slice($this->_items, 1, 1));
        }

        return $this->_entityFactory->create($this->_itemObjectClass);
    }

    /**
     * @return $this
     */
    public function addCommonFiltersForDashboard()
    {
        $this->addFilterByStatus(Status::STATUS_PASSED)
            ->orderByScheduled();

        return $this;
    }
}
