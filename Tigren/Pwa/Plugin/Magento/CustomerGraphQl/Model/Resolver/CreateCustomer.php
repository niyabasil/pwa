<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver;

use Magento\Framework\Exception\InputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\OrderCustomerDelegateInterface;
use Tigren\Pwa\Model\RequestHandler;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\Sales\Model\OrderFactory;

/**
 * Class CreateCustomer
 * @package Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver
 */
class CreateCustomer
{
    /**
     * @var RequestHandler
     */
    private $requestHandler;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var OrderCustomerDelegateInterface
     */
    private $delegateService;

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @param RequestHandler $requestHandler
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param OrderCustomerDelegateInterface $customerDelegation
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        RequestHandler $requestHandler,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        OrderCustomerDelegateInterface $customerDelegation,
        OrderFactory $orderFactory
    ) {
        $this->requestHandler = $requestHandler;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->delegateService = $customerDelegation;
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param $subject
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws InputException
     * @throws GraphQlInputException
     */
    public function beforeResolve(
        $subject,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $key = 'customer_create';

        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
            $this->requestHandler->execute($key);
        }

        if (!empty($args['input']['order_number'])) {
            $order = $this->orderFactory->create()->loadByIncrementId((int)$args['input']['order_number']);

            if ($order->getId()) {
                $this->delegateService->delegateNew($order->getId());
            }
        }
    }
}
