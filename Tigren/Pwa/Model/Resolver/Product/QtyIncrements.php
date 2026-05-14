<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class QtyIncrements
 * @package Tigren\Pwa\Model\Resolver\Product
 */
class QtyIncrements implements ResolverInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * PreOrder constructor.
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        StockRegistryInterface $stockRegistry
    ) {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var Product $product */
        $product = $value['model'];
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        if ($stockItem) {
            $qty = floatval($stockItem->getData('qty_increments'));
            $enableIncrements = $stockItem->getEnableQtyIncrements();
            return $qty && $enableIncrements ? $qty : 1;
        }
        return 1;
    }
}
