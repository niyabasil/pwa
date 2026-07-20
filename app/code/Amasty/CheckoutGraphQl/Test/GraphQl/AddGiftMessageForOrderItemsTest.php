<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

namespace Amasty\CheckoutGraphQl\Test\GraphQl;

use Magento\GraphQl\Quote\GetMaskedQuoteIdByReservedOrderId;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class AddGiftMessageForOrderItemsTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'addGiftMessageForOrderItems';
    public const ADDIT_QUERY_KEY = 'item_gift_message';
    public const CART_KEY = 'cart';

    /**
     * @var GetMaskedQuoteIdByReservedOrderId
     */
    private $getMaskedQuoteIdByReservedOrderId;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var QuoteResource
     */
    private $quoteResource;

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $this->getMaskedQuoteIdByReservedOrderId = $objectManager->get(GetMaskedQuoteIdByReservedOrderId::class);
        $this->quoteFactory = $objectManager->get(QuoteFactory::class);
        $this->quoteResource = $objectManager->get(QuoteResource::class);
    }

    /**
     * @group amasty_osc
     *
     * @magentoConfigFixture base_website sales/gift_options/allow_items 1
     *
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/guest/create_empty_cart.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/add_simple_product.php
     */
    public function testAddGiftMessageForOrderItems()
    {
        $reservedQuoteId = 'test_quote';
        $prodSku = 'simple_product';
        $itemId = 0;
        $itemGiftMessage = [
            'message' => 'item_gift_test_message',
            'recipient' => 'item_gift_test_recipient',
            'sender' => 'item_gift_test_sender'
        ];

        /** @var Quote $quote */
        $quote = $this->quoteFactory->create();
        $this->quoteResource->load($quote, $reservedQuoteId, 'reserved_order_id');

        /** @var Item $item */
        foreach ($quote->getAllItems() as $item) {
            if ($item->getSku() == $prodSku) {
                $itemId = $item->getId();
            }
        }

        $maskedQuote = $this->getMaskedQuoteIdByReservedOrderId->execute('test_quote');
        $query = $this->getMutation();
        $variables = [
            'cartId' => $maskedQuote,
            'itemId' => $itemId
        ];
        $response = $this->graphQlMutation($query, array_merge($variables, $itemGiftMessage));

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertEquals('Gift message for order items was applied.', $response[self::MAIN_QUERY_KEY]['response']);

        $this->assertArrayHasKey(self::CART_KEY, $response[self::MAIN_QUERY_KEY]);
        $this->assertArrayHasKey(self::ADDIT_QUERY_KEY, $response[self::MAIN_QUERY_KEY][self::CART_KEY]['items'][0]);
        $this->assertResponseFields(
            $response[self::MAIN_QUERY_KEY][self::CART_KEY]['items'][0][self::ADDIT_QUERY_KEY],
            $itemGiftMessage
        );
    }

    /**
     * @return string
     */
    private function getMutation(): string
    {
        return <<<'MUTATION'
mutation AddGiftMessageForOrderItems (
    $cartId: String!,
    $itemId: Int!,
    $message: String,
    $recipient: String,
    $sender: String
  ) {
  addGiftMessageForOrderItems(
    input: {
      cart_id:$cartId
      cart_items: {
        item_id:$itemId
        message:$message
        recipient:$recipient
        sender:$sender
      }
    }
  ){
    response
    cart {
      items{
        item_gift_message{
          message
          recipient
          sender
        }
      }
    }
  }
}
MUTATION;
    }
}
