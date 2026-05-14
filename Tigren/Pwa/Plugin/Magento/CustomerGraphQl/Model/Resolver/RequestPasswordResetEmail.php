<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver;

use Magento\Framework\Exception\InputException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Tigren\Pwa\Model\RequestHandler;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;

/**
 * Class RequestPasswordResetEmail
 * @package Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver
 */
class RequestPasswordResetEmail
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
     * @param RequestHandler $requestHandler
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     */
    public function __construct(
        RequestHandler $requestHandler,
        IsCaptchaEnabledInterface $isCaptchaEnabled
    ) {
        $this->requestHandler = $requestHandler;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
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
        $key = 'customer_forgot_password';

        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
            $this->requestHandler->execute($key);
        }
    }
}
