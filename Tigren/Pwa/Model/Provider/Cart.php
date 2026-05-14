<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Model\Provider;

/**
 * Class Cart
 * @package Tigren\Pwa\Model\Provider
 */
class Cart
{
    /**
     * @var
     */
    private $cart;

    /**
     * @return mixed
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param $cart
     * @return void
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

}
