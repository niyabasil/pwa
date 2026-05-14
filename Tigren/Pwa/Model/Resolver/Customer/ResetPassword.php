<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Validator\EmailAddress as EmailValidator;

/**
 * Class Resolver for ResetPassword
 */
class ResetPassword implements ResolverInterface
{
    /**
     * @var AccountManagementInterface
     */
    private $customerAccountManagement;

    /**
     * @var EmailValidator
     */
    private $emailValidator;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * ResetPassword constructor.
     *
     * @param AuthenticationInterface $authentication
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param EmailValidator $emailValidator
     */
    public function __construct(
        AuthenticationInterface $authentication,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        EmailValidator $emailValidator
    ) {
        $this->authentication = $authentication;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->emailValidator = $emailValidator;
    }

    /**
     * Reset old password and set new
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return bool|Value|mixed
     *
     * @throws GraphQlInputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args['resetPasswordToken'])) {
            throw new GraphQlInputException(__('resetPasswordToken must be specified'));
        }

        if (empty($args['newPassword'])) {
            throw new GraphQlInputException(__('newPassword must be specified'));
        }

        if (empty($args['confirmPassword'])) {
            throw new GraphQlInputException(__('confirmPassword must be specified'));
        }

        if ($args['newPassword'] !== $args['confirmPassword']) {
            throw new GraphQlInputException(__("New Password and Confirm New Password values didn't match."));
        }
        if (empty($args['customerId'])) {
            throw new GraphQlInputException(__('customer id must be specified'));
        }
        try {
            $customerData = $this->customerRepository->getById($args['customerId']);
            if (!empty($customerData->getEmail())) {
                return $this->customerAccountManagement->resetPassword(
                    $customerData->getEmail(),
                    $args['resetPasswordToken'],
                    $args['newPassword']
                );
            }
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }
    }
}
