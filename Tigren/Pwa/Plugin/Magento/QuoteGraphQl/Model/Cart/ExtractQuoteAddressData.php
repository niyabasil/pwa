<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Cart;

use Magento\Directory\Model\CountryFactory;
use Magento\Quote\Model\Quote\Address as QuoteAddress;

/**
 * Class ExtractQuoteAddressData
 * @package Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Cart
 */
class ExtractQuoteAddressData
{
    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        CountryFactory $countryFactory
    ) {
        $this->countryFactory = $countryFactory;
    }

    /**
     * @param \Magento\QuoteGraphQl\Model\Cart\ExtractQuoteAddressData $subject
     * @param $result
     * @param QuoteAddress $address
     * @return mixed
     */
    public function afterExecute(
        \Magento\QuoteGraphQl\Model\Cart\ExtractQuoteAddressData $subject,
        $result,
        QuoteAddress $address
    ) {
        $countryId = $address->getCountryId();
        $country = $this->countryFactory->create();

        if (!$countryId) {
            return $result;
        }

        return array_merge($result, [
            'country' => [
                'code' => $countryId,
                'label' => $country->loadByCode($countryId)->getName()
            ]
        ]);
    }
}
