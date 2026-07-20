<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

namespace Amasty\CheckoutGraphQl\Plugin\Customer\Helper\Address;

use Magento\Customer\Helper\Address;

class SetMinValue
{
    public function afterGetStreetLines(
        Address $subject,
        $result,
        $store = null
    ) {
        if ($result <= 1) {
            $result = 2;
        }

        return $result;
    }
}
