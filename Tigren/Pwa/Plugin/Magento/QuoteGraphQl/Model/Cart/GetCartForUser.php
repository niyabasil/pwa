<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Cart;

use Tigren\Pwa\Model\Provider\Cart;

/**
 * @inheritdoc
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GetCartForUser
{
    /**
     * @var Cart
     */
    private $cartProvider;

    public function __construct(
        Cart $cartProvider
    ) {
        $this->cartProvider = $cartProvider;
    }

    /**
     * @param \Magento\QuoteGraphQl\Model\Cart\GetCartForUser $subject
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\QuoteGraphQl\Model\Cart\GetCartForUser $subject,
        $result
    ) {
        $this->cartProvider->setCart($result);
        return $result;
    }
}
