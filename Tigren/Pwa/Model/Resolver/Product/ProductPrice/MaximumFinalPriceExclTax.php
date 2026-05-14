<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Product\ProductPrice;

use Magento\Catalog\Model\Product;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\CatalogGraphQl\Model\Resolver\Product\Price\ProviderPool as PriceProviderPool;

/**
 * Class BasePrice
 * @package Tigren\Pwa\Model\Resolver\Product
 */
class MaximumFinalPriceExclTax implements ResolverInterface
{
    /**
     * @var PriceProviderPool
     */
    private $priceProviderPool;

    /**
     * @param PriceProviderPool $priceProviderPool
     */
    public function __construct(
        PriceProviderPool $priceProviderPool
    ) {
        $this->priceProviderPool = $priceProviderPool;
    }

    /**
     * @param Field $field
     * @param $context
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
    ): array {
        /** @var Product $product */
        $product = $value['model'];
        if (empty($product)) {
            throw new GraphQlInputException(__('Must be has product model'));
        }
        $store = $context->getExtensionAttributes()->getStore();
        $priceProvider = $this->priceProviderPool->getProviderByProductType($product->getTypeId());
        $finalPriceExclTax = (float)$priceProvider->getMaximalFinalPrice($product)->getBaseAmount();

        return [
            'value' => $finalPriceExclTax,
            'currency' => $store->getCurrentCurrencyCode()
        ];
    }
}
