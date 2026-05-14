<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\CatalogGraphQl\Model\Resolver\Product;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Registry;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Correct the response data while adding product to cart
 */
class Options
{
    /**
     * @var Registry
     */
    private $coreRegister;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param Registry $coreRegister
     */
    public function __construct(
        Registry $coreRegister,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->coreRegister = $coreRegister;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @throws LocalizedException
     */
    public function beforeResolve(
        \Magento\CatalogGraphQl\Model\Resolver\Product\Options $subject,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        if (!empty($this->coreRegister->registry('current_product'))) {
            $this->coreRegister->unregister('current_product');
        }
        $this->coreRegister->register('current_product', $value['model']);
    }

    /**
     * @param \Magento\CatalogGraphQl\Model\Resolver\Product\Options $subject
     * @param $results
     * @return mixed
     */
    public function afterResolve(
        \Magento\CatalogGraphQl\Model\Resolver\Product\Options $subject,
        $results
    ) {
        if (is_array($results)) {
            foreach ($results as &$result) {
                if (isset($result['value'])) {
                    foreach ($result['value'] as &$value) {
                        if (isset($value['price']) && is_numeric($value['price'])) {
                            $value['price'] = $this->priceCurrency->convert((float)$value['price']);
                        }
                    }
                }
            }
        }

        return $results;
    }
}
