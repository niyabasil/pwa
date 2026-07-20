<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

namespace Amasty\CheckoutGraphQl\Test\GraphQl;

use Magento\GraphQl\Quote\GetMaskedQuoteIdByReservedOrderId;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\GraphQlAbstract;

class UpdateGiftWrapInformationTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'updateGiftWrapInformation';
    public const ADDIT_QUERY_KEY = 'amasty_gift_wrap';
    public const CART_KEY = 'cart';

    /**
     * @var GetMaskedQuoteIdByReservedOrderId
     */
    private $getMaskedQuoteIdByReservedOrderId;

    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = Bootstrap::getObjectManager();
        $this->getMaskedQuoteIdByReservedOrderId = $objectManager->get(GetMaskedQuoteIdByReservedOrderId::class);
    }

    /**
     * @group amasty_osc
     *
     * @magentoConfigFixture base_website amasty_checkout/gifts/gift_wrap 1
     * @magentoConfigFixture base_website amasty_checkout/gifts/gift_wrap_fee 17
     *
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/guest/create_empty_cart.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/add_simple_product.php
     */
    public function testUpdateGiftWrapInformation()
    {
        $giftWrapChecked = true;
        $amount = 17;
        $assertGiftWrapValue = [
            'amount' => $amount,
            'base_amount' => $amount,
            'currency_code' => "USD"
        ];

        $query = $this->getMutation();
        $variables =  [
            'cartId' => $this->getMaskedQuoteIdByReservedOrderId->execute('test_quote'),
            'checked' => $giftWrapChecked
        ];
        $response = $this->graphQlMutation($query, $variables);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertEquals('Gift wrap status was changed.', $response[self::MAIN_QUERY_KEY]['response']);
        $this->assertEquals($amount, $response[self::MAIN_QUERY_KEY]['amount']);
        $this->assertEquals($amount, $response[self::MAIN_QUERY_KEY]['base_amount']);

        $this->assertArrayHasKey(self::CART_KEY, $response[self::MAIN_QUERY_KEY]);
        $this->assertArrayHasKey(self::ADDIT_QUERY_KEY, $response[self::MAIN_QUERY_KEY][self::CART_KEY]);
        $this->assertResponseFields(
            $response[self::MAIN_QUERY_KEY][self::CART_KEY][self::ADDIT_QUERY_KEY],
            $assertGiftWrapValue
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
    $checked: Boolean!
  ) {
    updateGiftWrapInformation(
    input: {
      cart_id:$cartId
      checked:$checked
    }
  ){
    response
    amount
    base_amount
    cart {
      amasty_gift_wrap {
        amount
        base_amount
        currency_code
      }
    }
  }
}
MUTATION;
    }
}
