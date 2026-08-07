<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Blocks order placement when the shipping address is missing required fields.
 *
 * This catches a known failure mode where the cart page shipping estimator
 * (which stores only region + country) is left as the shipping address if the
 * full address mutation fails or is skipped during checkout. Without this guard
 * the order saves with dashes for firstname/lastname/street/city/telephone in
 * the Magento backend.
 */
class ValidateQuoteAddressObserver implements ObserverInterface
{
    /**
     * Required fields that must be non-empty on the shipping address.
     */
    private const REQUIRED_FIELDS = [
        'firstname',
        'lastname',
        'street',
        'city',
        'postcode',
        'telephone',
    ];

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        if (!$quote) {
            return;
        }

        // Virtual quotes (no physical shipping) have no shipping address.
        if ($quote->isVirtual()) {
            return;
        }

        $shippingAddress = $quote->getShippingAddress();

        if (!$shippingAddress) {
            throw new LocalizedException(__(
                'A shipping address is required to place your order. Please enter your full shipping address and try again.'
            ));
        }

        foreach (self::REQUIRED_FIELDS as $field) {
            $value = trim((string)$shippingAddress->getData($field));
            if ($value === '') {
                throw new LocalizedException(__(
                    'Your shipping address appears to be incomplete (missing %1). Please update your shipping address and try again.',
                    $field
                ));
            }
        }
    }
}
