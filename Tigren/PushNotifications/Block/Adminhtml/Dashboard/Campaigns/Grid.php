<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns;

use Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer\Clicks;
use Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid\Renderer\ScheduledAt;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data;

/**
 * Class Grid
 * @package Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns
 */
class Grid extends \Magento\Backend\Block\Dashboard\Grid
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lastCampaignsGrid');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->collectionFactory->create()->dashboardGridFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares page sizes for dashboard grid with las 5 orders
     *
     * @return void
     */
    protected function _preparePage()
    {
        $this->getCollection()->setPageSize($this->getParam($this->getVarNameLimit(), $this->_defaultLimit));
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'sortable' => false,
                'index' => 'name',
            ]
        );

        $this->addColumn(
            'scheduled',
            [
                'header' => __('Scheduled At'),
                'type' => 'date',
                'sortable' => false,
                'index' => 'scheduled',
                'renderer' =>
                    ScheduledAt::class,
            ]
        );

        $this->addColumn(
            'clicked',
            [
                'header' => __('Clicks'),
                'sortable' => false,
                'type' => 'number',
                'index' => 'clicked',
                'renderer' => Clicks::class,
            ]
        );

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/campaign/edit', ['id' => $row->getId()]);
    }
}
