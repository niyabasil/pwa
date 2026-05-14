<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Product\Options;

use Magento\Bundle\Pricing\Price\DiscountCalculator;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Registry;

/**
 * Class FinalPrice
 * @package Tigren\Pwa\Model\Resolver\Product\Options
 */
class FinalPrice implements ResolverInterface
{
    const NOT_EXCLUDE_ADJUSTMENT = ['tax'];

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * Event manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var DiscountCalculator
     */
    protected $discountCalculator;

    /**
     * @var Registry
     */
    private $coreRegister;

    /**
     * @param CalculatorInterface $calculator
     * @param ManagerInterface $eventManager
     * @param DiscountCalculator $discountCalculator
     * @param Registry $coreRegister
     */
    public function __construct(
        CalculatorInterface $calculator,
        ManagerInterface $eventManager,
        DiscountCalculator $discountCalculator,
        Registry $coreRegister
    ) {
        $this->calculator = $calculator;
        $this->eventManager = $eventManager;
        $this->discountCalculator = $discountCalculator;
        $this->coreRegister = $coreRegister;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return float
     * @throws GraphQlInputException
     * @throws NoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ): float {
        $parentProduct = $this->coreRegister->registry('current_product');
        $selectionPriceValue = !empty($value['price']) ? $value['price'] : 0 ;

        if (empty($parentProduct)) {
            throw new GraphQlInputException(__('Must be has parent product id'));
        }

        $excludeAdjustment = null;
        foreach ($parentProduct->getPriceInfo()->getAdjustments() as $adjustment) {
            if (!in_array($adjustment->getAdjustmentCode(), self::NOT_EXCLUDE_ADJUSTMENT)) {
                $excludeAdjustment[$adjustment->getAdjustmentCode()] = $adjustment->getAdjustmentCode();
            }
        }

        return (float)$this->calculator->getAmount(
            $selectionPriceValue,
            $parentProduct,
            $excludeAdjustment
        )->getValue();
    }
}
