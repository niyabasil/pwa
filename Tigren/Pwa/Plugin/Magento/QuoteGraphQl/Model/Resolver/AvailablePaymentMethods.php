<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver;

use Closure;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\Data\CartInterface;

/**
 * Class AvailablePaymentMethods
 * @package Tigren\Pwa\Plugin\Magento\QuoteGraphQl\Model\Resolver
 */
class AvailablePaymentMethods
{
    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var PaymentInformationManagementInterface
     */
    private $informationManagement;

    /**
     * @param PaymentInformationManagementInterface $informationManagement
     * @param Escaper $escaper
     */
    public function __construct(
        PaymentInformationManagementInterface $informationManagement,
        Escaper $escaper
    ) {
        $this->informationManagement = $informationManagement;
        $this->escaper = $escaper;
    }

    /**
     * @param \Magento\QuoteGraphQl\Model\Resolver\AvailablePaymentMethods $subject
     * @param array $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws LocalizedException
     */
    public function aroundResolve(
        \Magento\QuoteGraphQl\Model\Resolver\AvailablePaymentMethods $subject,
        Closure $proceed,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        $cart = $value['model'];
        return $this->getPaymentMethodsData($cart);
    }

    /**
     * Collect and return information about available payment methods
     *
     * @param CartInterface $cart
     * @return array
     */
    private function getPaymentMethodsData(CartInterface $cart): array
    {
        $paymentInformation = $this->informationManagement->getPaymentInformation($cart->getId());
        $paymentMethods = $paymentInformation->getPaymentMethods();

        $paymentMethodsData = [];
        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodsData[] = [
                'title' => $paymentMethod->getTitle(),
                'code' => $paymentMethod->getCode(),
                'instructions' => $this->getInstructions($paymentMethod)
            ];
        }
        return $paymentMethodsData;
    }

    /**
     * Get instructions text from config
     *
     * @param $paymentMethod
     * @return string
     */
    protected function getInstructions($paymentMethod)
    {
        return nl2br($this->escaper->escapeHtml($paymentMethod->getConfigData('instructions')));
    }
}
