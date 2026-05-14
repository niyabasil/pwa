<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\SalesGraphQl\Model\Formatter;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class Order
 * @package Tigren\Pwa\Plugin\Magento\SalesGraphQl\Model\Formatter
 */
class Order
{
    /**
     * @param \Magento\SalesGraphQl\Model\Formatter\Order $subject
     * @param $result
     * @param OrderInterface $orderModel
     * @return array
     */
    public function afterFormat(
        \Magento\SalesGraphQl\Model\Formatter\Order $subject,
        $result,
        OrderInterface $orderModel
    ): array {
        $result['state'] = $orderModel->getState();
        return $result;
    }
}
