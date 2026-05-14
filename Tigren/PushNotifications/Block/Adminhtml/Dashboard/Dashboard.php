<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\Dashboard;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Block\Adminhtml\Dashboard\Campaigns\Grid;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Status;
use Magento\Backend\Block\Dashboard\Grids;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Tigren\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory as SubscriberCollectionFactory;
use Magento\Framework\Phrase;

/**
 * Class Dashboard
 * @package Tigren\PushNotifications\Block\Adminhtml\Dashboard
 */
class Dashboard extends Template
{
    /**
     * @var string
     */
    protected $_template = 'dashboard/index.phtml';

    /**
     * @var CampaignCollectionFactory
     */
    private $campaignCollectionFactory;

    /**
     * @var SubscriberCollectionFactory
     */
    private $subscriberCollectionFactory;

    public function __construct(
        Context $context,
        CampaignCollectionFactory $campaignCollectionFactory,
        SubscriberCollectionFactory $subscriberCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addChild('lastCampaigns', Grid::class);
        $this->addChild('grids', Grids::class);

        parent::_prepareLayout();
    }

    /**
     * @return Phrase
     */
    public function getCampaignGridTitle()
    {
        return __('Latest campaigns');
    }

    /**
     * @return Phrase
     */
    public function getAddCampaignButtonTitle()
    {
        return __('+ Start a new Campaign');
    }

    /**
     * @return string
     */
    public function getAddCampaignButtonLink()
    {
        return $this->getUrl('*/campaign/edit');
    }

    /**
     * @return int
     */
    public function getFinishedCampaigns()
    {
        return $this->campaignCollectionFactory->create()->addFilterByStatus(Status::STATUS_PASSED)->getSize();
    }

    /**
     * @return int
     */
    public function getSubscribersCount()
    {
        return $this->subscriberCollectionFactory->create()->getSize();
    }

    /**
     * @return int
     */
    public function getLatestCampaignRate()
    {
        /** @var CampaignInterface $campaign */
        $campaign = $this->campaignCollectionFactory->create()->addCommonFiltersForDashboard()->getFirstItem();

        return $campaign ? $this->getCampaignClickRateInPercent($campaign) : 0;
    }

    /**
     * @param CampaignInterface $campaign
     * @return float|int
     */
    private function getCampaignClickRateInPercent(CampaignInterface $campaign)
    {
        return $campaign->getCampaignId() && $campaign->getShownCounter() !== 0
            ? number_format((($campaign->getClickedCounter() / $campaign->getShownCounter()) * 100), 2)
            : 0;
    }

    /**
     * @return int
     */
    public function getClicksTotal()
    {
        return $this->campaignCollectionFactory->create()->getClicksTotal();
    }

    /**
     * @return bool
     */
    public function isCountIncreased()
    {
        /** @var CampaignInterface $campaign */
        $campaign = $this->campaignCollectionFactory->create()->addCommonFiltersForDashboard()->getSecondItem();
        $campaignClickRate = $campaign ? $this->getCampaignClickRateInPercent($campaign) : 0;

        return $this->getLatestCampaignRate() > $campaignClickRate;
    }
}
