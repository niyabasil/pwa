<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

namespace Amasty\CheckoutGraphQl\Model\Utils;

class DDTimeDisplayValueGetter
{
    /**
     * @param int|string|null $time
     * @return string|null
     */
    public function getDisplayValue($time): ?string
    {
        if ($time !== null && $time >= 0) {
            return $time . ':00 - ' . (($time) + 1) . ':00';
        }

        return null;
    }
}
