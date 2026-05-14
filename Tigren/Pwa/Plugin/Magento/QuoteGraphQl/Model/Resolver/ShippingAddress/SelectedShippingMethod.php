<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver\ShippingAddress;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\Quote\Address;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Model\Config;

/**
 * Class SelectedShippingMethod
 * @package Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver\ShippingAddress
 */
class SelectedShippingMethod
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\QuoteGraphQl\Model\Resolver\ShippingAddress\SelectedShippingMethod $subject
     * @param $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws LocalizedException
     */
    public function afterResolve(
        \Magento\QuoteGraphQl\Model\Resolver\ShippingAddress\SelectedShippingMethod $subject,
        $result,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (is_array($result) && isset($result['amount'])) {
            /** @var Address $address */
            $address = $value['model'];
            $displayCartPricesTax = (int)$this->_scopeConfig->getValue(
                Config::XML_PATH_DISPLAY_CART_SHIPPING,
                ScopeInterface::SCOPE_STORE,
                $context->getExtensionAttributes()->getStore()
            );

            switch ($displayCartPricesTax) {
                case Config::DISPLAY_TYPE_EXCLUDING_TAX:
                    $result['amount']['value'] = $address->getShippingAmount();
                    break;
                case Config::DISPLAY_TYPE_INCLUDING_TAX:
                    $result['amount']['value'] = $address->getShippingInclTax();
                    break;
                case Config::DISPLAY_TYPE_BOTH:
                    break;
            }
        }

        return $result;
    }
}
