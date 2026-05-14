<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\PushNotifications\Observer;

use Tigren\PushNotifications\Model\ConfigProvider;
use Tigren\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory;
use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SalesOrderSaveAfter
 * @package Tigren\PushNotifications\Observer
 */
class SalesOrderSaveAfter implements ObserverInterface
{
    /**
     *
     */
    const XML_PATH_NOTIFICATIONS_ORDER_NOTIFICATIONS_ENABLED = 'tigren_notifications/order_notifications/enabled';

    /**
     *
     */
    const XML_PATH_NOTIFICATIONS_ORDER_NOTIFICATIONS_SETTINGS = 'tigren_notifications/order_notifications/settings';

    /**
     *
     */
    const XML_PATH_NOTIFICATIONS_DESIGN_LOGO = 'tigren_notifications/design/logo';

    /**
     *
     */
    const TOPIC_NAME = 'tigren.notifications.sales.order.status.update';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var CollectionFactory
     */
    private $subscriberCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderSaveAfter constructor.
     *
     * @param ConfigProvider $configProvider
     * @param CollectionFactory $subscriberCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param PublisherInterface $publisher
     * @param SerializerInterface $serializer
     * @param FilterProvider $filterProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        ConfigProvider $configProvider,
        CollectionFactory $subscriberCollectionFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        PublisherInterface $publisher,
        SerializerInterface $serializer,
        FilterProvider $filterProvider,
        LoggerInterface $logger
    ) {
        $this->configProvider = $configProvider;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->publisher = $publisher;
        $this->serializer = $serializer;
        $this->filterProvider = $filterProvider;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return false|void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var OrderInterface $order */
        $order = $observer->getOrder();
        $storeId = $this->storeManager->getStore()->getId();

        if ($order->dataHasChangedFor('status')
            && $this->configProvider->isModuleEnable()
            && $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATIONS_ORDER_NOTIFICATIONS_ENABLED,
                ScopeInterface::SCOPE_STORE, $storeId)
        ) {
            $notificationSettings = $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATIONS_ORDER_NOTIFICATIONS_SETTINGS);
            if (!$notificationSettings) {
                return false;
            }

            $customerId = $order->getCustomerId();
            if (!$customerId) {
                return false;
            }

            $subscriber = $this->subscriberCollectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->getFirstItem();
            if (!$subscriber->getId()) {
                return false;
            }

            $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $content = $this->convertCsvToArray($notificationSettings);
            foreach ($content as $type) {
                if ($this->canPostNotification($subscriber, $type, $order)) {
                    try {
                        $variable = [
                            'increment_id' => $order->getIncrementId(),
                            'status_label' => $order->getStatusLabel(),
                            'order' => $order,
                        ];
                        $this->filterProvider->getBlockFilter()->setVariables($variable);

                        $params = [
                            'to' => $subscriber->getToken(),
                            'data' => [
                                'title' => $this->filterProvider->getBlockFilter()->filter($type['title']),
                                'body' => $this->filterProvider->getBlockFilter()->filter($type['message']),
                                'image' => $mediaUrl . $this->scopeConfig->getValue(self::XML_PATH_NOTIFICATIONS_DESIGN_LOGO),
                                'type' => 'order',
                                'entity_id' => $order->getEntityId()
                            ]
                        ];
                        $this->publisher->publish(self::TOPIC_NAME, $this->serializer->serialize($params));
                    } catch (Exception $e) {
                        $this->logger->error($e);
                    }

                    break;
                }
            }
        }
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param bool $addHeader
     * @return array
     */
    protected function convertCsvToArray($string = '', $delimiter = '|', $addHeader = true)
    {
        $enclosure = '"';
        $escape = "\\";

        $rows = array_filter(preg_split('/\r*\n+|\r+/', $string));

        $data = [];
        if ($addHeader) {
            $header = array_shift($rows);
            $header = str_getcsv($header, $delimiter, $enclosure, $escape);

            foreach ($rows as $row) {
                $row = str_getcsv($row, $delimiter, $enclosure, $escape);
                $data[] = array_combine($header, $row);
            }
        } else {
            foreach ($rows as $row) {
                $data[] = str_getcsv($row, $delimiter, $enclosure, $escape);
            }
        }

        return $data;
    }

    /**
     * @param $subscriber
     * @param $type
     * @param $order
     * @return bool
     */
    private function canPostNotification($subscriber, $type, $order)
    {
        if ($subscriber->getId()
            && !empty($type['status'])
            && $type['status'] === $order->getStatus()
            && !empty($type['title'])
            && !empty($type['message'])
        ) {
            return true;
        }

        return false;
    }
}
