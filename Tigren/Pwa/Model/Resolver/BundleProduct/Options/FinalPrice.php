<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\BundleProduct\Options;

use Magento\Bundle\Pricing\Price\BundleOptionPrice;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Tigren\Pwa\Helper\Data as TigrenPwaHelper;

/**
 * Class FinalPrice
 * @package Tigren\Pwa\Model\Resolver\BundleProduct\Options
 */
class FinalPrice implements ResolverInterface
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var TigrenPwaHelper
     */
    private $tigrenPwaHelper;

    /**
     * @var array
     */
    private $parentProducts = [];

    /**
     * @param ProductFactory $productFactory
     * @param TigrenPwaHelper $tigrenPwaHelper
     */
    public function __construct(
        ProductFactory $productFactory,
        TigrenPwaHelper $tigrenPwaHelper
    ) {
        $this->productFactory = $productFactory;
        $this->tigrenPwaHelper = $tigrenPwaHelper;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return float|Value|mixed
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $parentProductId = $value['parent_product_id'];
        $selectionId = $value['selection_id'];

        if (empty($parentProductId)) {
            throw new GraphQlInputException(__('Must be has parent product id'));
        }
        if (empty($selectionId)) {
            throw new GraphQlInputException(__('Must be has selection id'));
        }

        if (empty($this->parentProducts[$parentProductId])) {
            $this->parentProducts[$parentProductId] = $this->productFactory->create()->load($parentProductId);
        }
        $productSelectionItems = $this->tigrenPwaHelper->getProductSelectionItems($this->parentProducts[$parentProductId]);

        if (isset($productSelectionItems[$selectionId])) {
            $selection = $productSelectionItems[$selectionId];

            /** @var BundleOptionPrice $price */
            $price = $this->parentProducts[$parentProductId]->getPriceInfo()->getPrice('bundle_option');
            $amount = $price->getOptionSelectionAmount($selection);

            return $amount->getValue();
        }

        return null;
    }
}
