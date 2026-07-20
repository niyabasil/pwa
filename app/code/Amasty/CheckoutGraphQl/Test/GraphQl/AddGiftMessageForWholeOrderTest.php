<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

namespace Amasty\CheckoutGraphQl\Test\GraphQl;

use Magento\GraphQl\Quote\GetMaskedQuoteIdByReservedOrderId;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class AddGiftMessageForWholeOrderTest extends GraphQlAbstract
{
    public const CUSTOMER_NAME = 'customer@example.com';
    public const CUSTOMER_PASS = 'password';
    public const MAIN_QUERY_KEY = 'addGiftMessageForWholeOrder';
    public const ADDIT_QUERY_KEY = 'order_gift_message';
    public const CART_KEY = 'cart';

    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @var GetMaskedQuoteIdByReservedOrderId
     */
    private $getMaskedQuoteIdByReservedOrderId;

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $this->customerTokenService = $objectManager->get(CustomerTokenServiceInterface::class);
        $this->getMaskedQuoteIdByReservedOrderId = $objectManager->get(GetMaskedQuoteIdByReservedOrderId::class);
    }

    /**
     * @group amasty_osc
     *
     * @magentoConfigFixture base_website sales/gift_options/allow_order 1
     *
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoApiDataFixture Amasty_CheckoutGraphQl::Test/GraphQl/_files/customer/create_empty_cart.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/add_simple_product.php
     */
    public function testAddGiftMessageForWholeOrder()
    {
        $reservedQuoteId = 'test_quote';
        $orderGiftMessage = [
            'message' => 'item_gift_test_message',
            'recipient' => 'item_gift_test_recipient',
            'sender' => 'item_gift_test_sender'
        ];

        $maskedQuote = $this->getMaskedQuoteIdByReservedOrderId->execute($reservedQuoteId);
        $query = $this->getMutation();
        $variables =  [
            'cartId' => $maskedQuote
        ];
        $response = $this->graphQlMutation($query, array_merge($variables, $orderGiftMessage), '', $this->getHeader());

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertEquals('Gift message for whole order was applied.', $response[self::MAIN_QUERY_KEY]['response']);

        $this->assertArrayHasKey(self::CART_KEY, $response[self::MAIN_QUERY_KEY]);
        $this->assertArrayHasKey(self::ADDIT_QUERY_KEY, $response[self::MAIN_QUERY_KEY][self::CART_KEY]);
        $this->assertResponseFields(
            $response[self::MAIN_QUERY_KEY][self::CART_KEY][self::ADDIT_QUERY_KEY],
            $orderGiftMessage
        );
    }

    /**
     * @return string
     */
    private function getMutation(): string
    {
        return <<<'MUTATION'
mutation AddGiftMessageForWholeOrder (
    $cartId: String!,
    $message: String,
    $recipient: String,
    $sender: String
  ) {
    addGiftMessageForWholeOrder(
    input: {
      cart_id:$cartId
      message:$message
      recipient:$recipient
      sender:$sender
    }
  ){
    response
    cart {
      order_gift_message {
        message
        recipient
        sender
      }
    }
  }
}
MUTATION;
    }

    /**
     * @return string[]
     */
    private function getHeader(): array
    {
        $customerToken = $this->customerTokenService->createCustomerAccessToken(
            self::CUSTOMER_NAME,
            self::CUSTOMER_PASS
        );

        return ['Authorization' => 'Bearer ' . $customerToken];
    }
}
