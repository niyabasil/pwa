<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\CatalogGraphQl\Model\Resolver;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\GraphQl\Query\Resolver\ArgumentsProcessorInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Products
 * @package Tigren\Pwa\Plugin\Magento\CatalogGraphQl\Model\Resolver
 */
class Products
{
    /**
     * @var ArgumentsProcessorInterface
     */
    private $argsSelection;

    /**
     * @param ArgumentsProcessorInterface|null $argsSelection
     */
    public function __construct(
        ArgumentsProcessorInterface $argsSelection = null,
    ) {
        $this->argsSelection = $argsSelection ?: ObjectManager::getInstance()
            ->get(ArgumentsProcessorInterface::class);
    }

    /**
     * @throws LocalizedException
     */
    public function beforeResolve(
        \Magento\CatalogGraphQl\Model\Resolver\Products $subject,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $fieldName = $info->fieldName;
        if ($fieldName) {
            $args = $this->argsSelection->process((string)$info->fieldName, $args);
        }

        return [$field, $context, $info, $value, $args];
    }
}
