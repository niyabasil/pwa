<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\PushNotifications\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;

/**
 * Class GetCustomerIp
 * @package Tigren\PushNotifications\Model
 */
class GetCustomerIp
{
    /**
     * Local IP address
     */
    const LOCAL_IP = '127.0.0.1';

    /**
     * @var string[]
     */
    protected $addressPath = [
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    ];

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * GetCustomerIp constructor.
     *
     * @param RemoteAddress $remoteAddress
     * @param RequestInterface $request
     */
    public function __construct(
        RemoteAddress $remoteAddress,
        RequestInterface $request
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->request = $request;
    }

    /**
     * @return string
     */
    public function getCurrentIp()
    {
        foreach ($this->addressPath as $path) {
            $ip = $this->request->getServer($path);
            if ($ip) {
                if (strpos($ip, ',') !== false) {
                    $addresses = explode(',', $ip);
                    foreach ($addresses as $address) {
                        if (trim($address) !== self::LOCAL_IP) {
                            return trim($address);
                        }
                    }
                } else {
                    if ($ip !== self::LOCAL_IP) {
                        return $ip;
                    }
                }
            }
        }

        return $this->remoteAddress->getRemoteAddress();
    }
}
