<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Ui\Component\Listing\Column;

use Tigren\PushNotifications\Api\Data\SubscriberInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class SubscriberIp
 * @package Tigren\PushNotifications\Ui\Component\Listing\Column
 */
class SubscriberIp extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[SubscriberInterface::SUBSCRIBER_IP])) {
                    $item[SubscriberInterface::SUBSCRIBER_IP] =
                        $this->anonymizeIp($item[SubscriberInterface::SUBSCRIBER_IP]);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param string $ip
     *
     * @return string
     */
    private function anonymizeIp($ip)
    {
        $ipArray = explode('.', $ip);
        $lastElementKey = key(array_slice($ipArray, -1, 1, true));
        $ipArray[$lastElementKey] = '**';

        return implode('.', $ipArray);
    }
}
