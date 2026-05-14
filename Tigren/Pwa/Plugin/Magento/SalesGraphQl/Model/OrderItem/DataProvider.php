<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\SalesGraphQl\Model\OrderItem;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Class DataProvider
 * @package Tigren\Pwa\Plugin\Magento\SalesGraphQl\Model\OrderItem
 */
class DataProvider
{
    /**
     * @throws GraphQlInputException
     */
    public function afterGetOrderItemById(
        \Magento\SalesGraphQl\Model\OrderItem\DataProvider $subject,
        $result,
        int $orderItemId
    ) {
        $orderItemModel = !empty($result['model']) ? $result['model'] : null;

        if (empty($orderItemModel)) {
            throw new GraphQlInputException(__('"model" value should be specified'));
        }

        $productOptions = $orderItemModel->getProductOptions();
        $options = !empty($productOptions['options']) ? $productOptions['options'] : [];

        return array_merge($result, [
            'customize_options' => $options
        ]);
    }
}
