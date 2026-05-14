<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory;

/**
 * Class CronNotification
 * @package Tigren\PushNotifications\Block\Adminhtml
 */
class CronNotification extends Container
{
    /**
     * Url
     */
    const URL_CRON = 'https://tigren.com/blog/configure-magento-cron-job';

    /**
     * @var string
     */
    protected $_template = 'cron_notification.phtml';

    /**
     * @var CollectionFactory
     */
    private $cronCollectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $cronCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cronCollectionFactory = $cronCollectionFactory;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $crontabCollection = $this->cronCollectionFactory->create();

        if ($crontabCollection->getSize() !== 0) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getNotificationMessage()
    {
        return __('Magento cron doesn\'t seem to be running. Please check <a target="_blank" href="%1">this article</a>
                   to learn why Magento cron is important and how to configure it.', $this->getSectionLink());
    }

    /**
     * @return string
     */
    private function getSectionLink()
    {
        return self::URL_CRON;
    }
}
