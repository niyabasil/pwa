<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$objectManager = Bootstrap::getObjectManager();

/** @var CartManagementInterface $cartManagement */
$cartManagement = $objectManager->get(CartManagementInterface::class);

/** @var CartRepositoryInterface $cartRepository */
$cartRepository = $objectManager->get(CartRepositoryInterface::class);

/** @var QuoteIdMaskFactory $quoteIdMaskFactory */
$quoteIdMaskFactory = $objectManager->get(QuoteIdMaskFactory::class);

/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->create(CustomerRepositoryInterface::class);

$customer = $customerRepository->get('customer@example.com');
$customerId = $customer->getId();

$cartId = $cartManagement->createEmptyCartForCustomer($customerId);
$cart = $cartRepository->get($cartId);
$cart->setReservedOrderId('test_quote');
$cartRepository->save($cart);

$quoteIdMask = $quoteIdMaskFactory->create();
$quoteIdMask->setQuoteId($cartId)
    ->save();
