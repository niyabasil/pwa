<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\Customer\Model;

use Magento\Framework\Exception\InvalidEmailOrPasswordException;

/**
 * Class Authentication
 * @package Tigren\Pwa\Plugin\Magento\Customer\Model
 */
class Authentication extends \Magento\Customer\Model\Authentication
{
    /**
     * @param \Magento\Customer\Model\Authentication $subject
     * @param \Closure $proceed
     * @param $customerId
     * @param $password
     * @return mixed
     * @throws InvalidEmailOrPasswordException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundAuthenticate(
        \Magento\Customer\Model\Authentication $subject,
        \Closure $proceed,
        $customerId,
        $password
    ) {
        $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
        $hash = $customerSecure->getPasswordHash() ? $customerSecure->getPasswordHash() : '';
        if (!$this->encryptor->validateHash($password, $hash)) {
            throw new InvalidEmailOrPasswordException(__("The password doesn't match this account. Verify the password and try again."));
        }

        return $proceed($customerId, $password);
    }
}