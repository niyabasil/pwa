<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Customer;

use Exception;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Validator\EmailAddress as EmailValidator;

/**
 * Class Resolver for RequestPasswordResetEmail
 */
class RequestPasswordResetEmail implements ResolverInterface
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
     * RequestPasswordResetEmail constructor.
     *
     * @param AccountManagementInterface $customerAccountManagement
     * @param EmailValidator $emailValidator
     */
    public function __construct(
        AccountManagementInterface $customerAccountManagement,
        EmailValidator $emailValidator
    ) {
        $this->customerAccountManagement = $customerAccountManagement;
        $this->emailValidator = $emailValidator;
    }

    /**
     * Send password email request
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     *
     * @return bool
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
        if (empty($args['email'])) {
            throw new GraphQlInputException(__('You must specify an email address.'));
        }

        if (!$this->emailValidator->isValid($args['email'])) {
            throw new GraphQlInputException(__('The email address has an invalid format.'));
        }

        try {
            return $this->customerAccountManagement->initiatePasswordReset(
                $args['email'],
                AccountManagement::EMAIL_RESET
            );
        } catch (NoSuchEntityException $e) {
            // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
            return true;
        } catch (SecurityViolationException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        } catch (Exception $e) {
            throw new GraphQlInputException(__('Cannot reset the customer\'s password.' . $e->getMessage()), $e);
        }
    }
}
