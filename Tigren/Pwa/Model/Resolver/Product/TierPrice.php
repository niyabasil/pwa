<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Locale\Format;

/**
 * Class TierPrice
 * @package Tigren\Pwa\Model\Resolver\Product
 */
class TierPrice implements ResolverInterface
{
    /**
     * @var Format
     */
    private $localeFormat;

    /**
     * TierPrice constructor.
     * @param Format|null $localeFormat
     */
    public function __construct(
        Format $localeFormat = null
    ) {
        $this->localeFormat = $localeFormat ?: ObjectManager::getInstance()->get(Format::class);
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        $product = $value['model'];
        if (!$product->getId()) {
            return [];
        }

        $tierPrices = [];

        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            if (!empty($this->getAllowProducts($product))) {
                foreach ($this->getAllowProducts($product) as $childProduct) {
                    $childProductTierPrice = $this->getTierPricesByProduct($childProduct);
                    if (!empty($childProductTierPrice)) {
                        $tierPrices = array_merge($childProductTierPrice);
                    }
                }
            }
        } elseif ($product->getTypeId() == Type::TYPE_SIMPLE) {
            $tierPrices = $this->getTierPricesByProduct($product);
        }
        return $tierPrices;
    }

    /**
     * Get Allowed Products
     * @param \Magento\Catalog\Model\Product $parentProduct
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getAllowProducts(\Magento\Catalog\Model\Product $parentProduct)
    {
        $products = [];
        $allProducts = $parentProduct->getTypeInstance()->getUsedProducts($parentProduct, null);
        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($allProducts as $product) {
            if ((int)$product->getStatus() === Status::STATUS_ENABLED) {
                $products[] = $product;
            }
        }
        return $products;
    }

    /**
     * Returns product's tier prices list
     *
     * @param ProductInterface $product
     * @return array
     */
    private function getTierPricesByProduct(ProductInterface $product): array
    {
        $tierPrices = [];
        $tierPriceModel = $product->getPriceInfo()->getPrice('tier_price');
        foreach ($tierPriceModel->getTierPriceList() as $tierPrice) {
            $tierPriceData = [
                'item_id' => $product->getId(),
                'qty' => $this->localeFormat->getNumber($tierPrice['price_qty']),
                'price' => $this->localeFormat->getNumber($tierPrice['price']->getValue()),
                'percentage' => $this->localeFormat->getNumber(
                    $tierPriceModel->getSavePercent($tierPrice['price'])
                ),
            ];

            if (isset($tierPrice['excl_tax_price'])) {
                $excludingTax = $tierPrice['excl_tax_price'];
                $tierPriceData['excl_tax_price'] = $this->localeFormat->getNumber($excludingTax->getBaseAmount());
            }
            $tierPrices[] = $tierPriceData;
        }
        return $tierPrices;
    }
}
