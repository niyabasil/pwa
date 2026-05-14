<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Sales\TrackingOrder;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\SalesGraphQl\Model\OrderItem\DataProvider as OrderItemProvider;

/**
 * Resolve order items for order
 */
class Items implements ResolverInterface
{
    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @var OrderItemProvider
     */
    private $orderItemProvider;

    /**
     * @param ValueFactory $valueFactory
     * @param OrderItemProvider $orderItemProvider
     */
    public function __construct(
        ValueFactory $valueFactory,
        OrderItemProvider $orderItemProvider
    ) {
        $this->valueFactory = $valueFactory;
        $this->orderItemProvider = $orderItemProvider;
    }


    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!(($value['model'] ?? null) instanceof OrderInterface)) {
            throw new GraphQlInputException(__('"model" value should be specified'));
        }

        /** @var OrderInterface $order */
        $order = $value['model'];
        $orderItemIds = [];
        foreach ($order->getItems() as $item) {
            if (!$item->getParentItemId()) {
                $orderItemIds[] = (int)$item->getItemId();
            }
            $this->orderItemProvider->addOrderItemId((int)$item->getItemId());
        }

        $itemsList = [];
        foreach ($orderItemIds as $orderItemId) {
            $itemsList[] = $this->valueFactory->create(
                function () use ($orderItemId) {
                    return $this->orderItemProvider->getOrderItemById((int)$orderItemId);
                }
            );
        }

        return $itemsList;
    }
}
