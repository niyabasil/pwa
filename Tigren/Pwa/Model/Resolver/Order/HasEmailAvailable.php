<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Order;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class HasEmailAvailable
 * @package Tigren\Pwa\Model\Resolver\Order
 */
class HasEmailAvailable implements ResolverInterface
{
    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        AccountManagementInterface $accountManagement
    ) {
        $this->accountManagement = $accountManagement;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $order = $value['model'];
        if (!empty($order)) {
            return $this->accountManagement->isEmailAvailable($order->getCustomerEmail());
        }

        return false;
    }
}
