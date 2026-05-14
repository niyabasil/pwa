<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;

/**
 * Correct the response data while adding product to cart
 */
class AddProductsToCart
{
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @param GetCartForUser $getCartForUser
     */
    public function __construct(
        GetCartForUser $getCartForUser
    ) {
        $this->getCartForUser = $getCartForUser;
    }

    /**
     * @param $subject
     * @param $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return mixed|Value
     * @throws GraphQlInputException
     * @throws NoSuchEntityException
     * @throws GraphQlAuthorizationException
     * @throws GraphQlNoSuchEntityException
     */
    public function afterResolve(
        $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args['cartId'])) {
            throw new GraphQlInputException(__('Required parameter "cartId" is missing'));
        }

        if (empty($args['cartItems']) || !is_array($args['cartItems'])) {
            throw new GraphQlInputException(__('Required parameter "cartItems" is missing'));
        }

        if (is_array($result) && isset($result['cart']['model'])) {
            $maskedCartId = $args['cartId'];
            $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
            $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);
            $result['cart']['model'] = $cart;
        }

        return $result;
    }
}
