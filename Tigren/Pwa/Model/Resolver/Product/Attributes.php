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
use Magento\Catalog\Model\ProductRepository;

/**
 * Class Attributes
 * @package Tigren\Pwa\Model\Resolver\Product
 */
class Attributes implements ResolverInterface
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var
     */
    protected $productRepository;

    /**
     * Attributes constructor.
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductRepository $productRepository
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        ProductRepository $productRepository
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->productRepository = $productRepository;
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
        $data = [];
        $product = $this->productRepository->getById($product->getId());

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
