<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Ui\Component\Listing\Column;

use Tigren\PushNotifications\Controller\RegistryConstants;
use Tigren\PushNotifications\Model\ConfigProvider;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class TestNotification
 * @package Tigren\PushNotifications\Ui\Component\Listing\Column
 */
class TestNotification extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ConfigProvider $configProvider,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->configProvider = $configProvider;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {
                if (!$this->configProvider->isModuleEnable()) {
                    $item[$fieldName . '_html'] = '-';
                } else {
                    $item[$fieldName . '_html'] = "<button class='button'><span>"
                        . $this->getButtonLabel() . "</span></button>";
                    $item[$fieldName . '_urlAction'] = $this->urlBuilder
                        ->getUrl('tigren_notifications/campaign/sendTest');
                    $item[$fieldName . '_senderId'] = $this->configProvider->getSenderId();
                }
            }
        }

        return $dataSource;
    }

    /**
     * @return Phrase
     */
    private function getButtonLabel()
    {
        return __('Send Test Notification');
    }
}
