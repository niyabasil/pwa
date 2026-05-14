<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\Api;

use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\ConfigProvider;
use Tigren\PushNotifications\Model\Curl\Curl;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Sender
 * @package Tigren\PushNotifications\Model\Api
 */
class Sender implements SenderInterface
{
    /**
     * @var string
     */
    private $contentType = 'application/json';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Json
     */
    private $json;

    /**
     * Sender constructor.
     *
     * @param ConfigProvider $configProvider
     * @param Curl $curl
     * @param Json $json
     */
    public function __construct(
        ConfigProvider $configProvider,
        Curl $curl,
        Json $json
    ) {
        $this->configProvider = $configProvider;
        $this->curl = $curl;
        $this->json = $json;
    }

    /**
     * @inheritdoc
     */
    public function send(array $params, $storeId = null)
    {
        $response = [];
        $curlBody = $this->curlSend($params, $storeId);

        if ($curlBody) {
            $response = $this->json->unserialize($curlBody);
        }

        return $response;
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    private function getApiKey($storeId = null)
    {
        return $this->configProvider->getFirebaseApiKey($storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    private function getRequestHeaders($storeId = null)
    {
        return [
            'Content-Type' => $this->contentType,
            'Authorization' => 'key=' . $this->getApiKey($storeId),
        ];
    }

    /**
     * @param $data
     * @return string
     */
    private function prepareRequestBodyToSend($data)
    {
        return $this->json->serialize($data);
    }

    /**
     * @param array $params
     * @param int|null $storeId
     *
     * @return string
     */
    private function curlSend($params, $storeId = null)
    {
        $this->curl->setHeaders($this->getRequestHeaders($storeId));
        $this->curl->setOption(CURLOPT_FOLLOWLOCATION, 1);
        $this->curl->post($this->configProvider->getFirebaseApiRequestUrl(), $this->prepareRequestBodyToSend($params));

        return $this->curl->getBody();
    }
}
