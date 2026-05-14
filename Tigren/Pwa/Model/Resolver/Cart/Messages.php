<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Cart;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Message\MessageInterface;
use Magento\Quote\Model\Quote;

/**
 * Class Messages
 */
class Messages implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var Quote $cart */
        $cart = $value['model'];

        $cartMessages = [];
        /** @var MessageInterface $message */
        foreach ($cart->getMessages() as $message) {
            $cartMessages[] = [
                'type' => $message->getType(),
                'text' => $message->getText()
            ];
        }

        return $cartMessages;
    }
}
