<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Product;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class ProductAttribute
 * @package Tigren\Pwa\Model\Resolver\Product
 */
class ProductAttribute implements ResolverInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductFactory $productFactory
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        ProductFactory $productFactory
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->productFactory = $productFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($args['product_id'])) {
            throw new LocalizedException(__('"product_id" value should be specified'));
        }
        $product = $this->productFactory->create()->load($args['product_id']);
        $data = [];
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($this->isVisibleOnFrontend($attribute, [])) {
                $value = $attribute->getFrontend()->getValue($product);

                if ($value instanceof Phrase) {
                    $value = (string)$value;
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }

                if (is_string($value) && strlen($value)) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode(),
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Determine if we should display the attribute on the front-end
     *
     * @param AbstractAttribute $attribute
     * @param array $excludeAttr
     * @return bool
     */
    protected function isVisibleOnFrontend(
        AbstractAttribute $attribute,
        array $excludeAttr
    ) {
        return ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr));
    }
}
