<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Event\ManagerInterface;

/**
 * Class Product
 * @package Tigren\Pwa\Model\Resolver
 */
class Product implements ResolverInterface
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * Event manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param ProductFactory $productFactory
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        ProductFactory $productFactory,
        ManagerInterface $eventManager
    ) {
        $this->productFactory = $productFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $productId = $this->getProductId($args);
        return $this->getProductData($productId, !empty($args['is_detail']));
    }

    /**
     * @param array $args
     * @return int
     * @throws GraphQlInputException
     */
    private function getProductId(array $args): int
    {
        if (!isset($args['id'])) {
            throw new GraphQlInputException(__('"Product id should be specified'));
        }

        return (int)$args['id'];
    }

    /**
     * @param int $productId
     * @param bool $isDetailPage
     * @return array
     * @throws GraphQlNoSuchEntityException
     */
    private function getProductData(int $productId, bool $isDetailPage): array
    {
        $product = $this->productFactory->create()->load($productId);
        if (!$product->getId()) {
            throw new GraphQlNoSuchEntityException(__('Product doesn\'t exist'));
        }

        if ($isDetailPage) {
            $this->eventManager->dispatch('catalog_controller_product_view', ['product' => $product]);
        }
        $productData = $product->getData();
        $productData['model'] = $product;

        return [
            'item' => $productData
        ];
    }
}
