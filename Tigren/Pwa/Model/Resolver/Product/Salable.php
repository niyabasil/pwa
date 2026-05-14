<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Product;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;

/**
 * Class Salable
 */
class Salable implements ResolverInterface
{
    /**
     * @var GetSalableQuantityDataBySku
     */
    protected $getSalableQuantityDataBySku;

    /**
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     */
    public function __construct(
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku
    ) {
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return int|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $salable = $this->getSalableQuantityDataBySku->execute($value['sku']);
        if (empty($salable)) {
            return 0;
        }

        return $salable[0]['qty'];
    }
}
