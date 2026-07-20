<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\ResourceModel\Quote;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();

/** @var QuoteFactory $quoteFactory */
$quoteFactory = $objectManager->get(QuoteFactory::class);

/** @var Quote $quoteResource */
$quoteResource = $objectManager->get(Quote::class);

/** @var QuoteIdMaskFactory $quoteIdMaskFactory */
$quoteIdMaskFactory = $objectManager->get(QuoteIdMaskFactory::class);

$quote = $quoteFactory->create();
$quoteResource->load($quote, 'test_quote', 'reserved_order_id');
$quoteResource->delete($quote);

/** @var QuoteIdMask $quoteIdMask */
$quoteIdMask = $quoteIdMaskFactory->create();
$quoteIdMask->setQuoteId($quote->getId())
    ->delete();

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');
