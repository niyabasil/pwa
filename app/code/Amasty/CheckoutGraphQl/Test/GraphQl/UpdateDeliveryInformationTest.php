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

class UpdateDeliveryInformationTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'updateDeliveryInformation';
    public const ADDIT_QUERY_KEY = 'amasty_delivery_date';
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
     * @magentoConfigFixture default_store amasty_checkout/delivery_date/enabled 1
     * @magentoConfigFixture default_store amasty_checkout/delivery_date/available_hours 8-16
     * @magentoConfigFixture default_store amasty_checkout/delivery_date/delivery_comment_enable 1
     *
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/guest/create_empty_cart.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/add_simple_product.php
     */
    public function testUpdateDeliveryInformation()
    {
        $deliveryComment = "delivery_test_comment";
        $timeValue = 9;
        $assertDeliveryValues = [
            'comment' => $deliveryComment,
            'date' => "2023-05-12",
            'time' => [
                'displayValue' => '9:00 - 10:00',
                'value' => $timeValue
            ]
        ];

        $maskedQuote = $this->getMaskedQuoteIdByReservedOrderId->execute('test_quote');
        $query = $this->getMutation();
        $variables =  [
            'cartId' => $maskedQuote,
            'comment' => $deliveryComment,
            'date' => "05/12/2023",
            'time' => $timeValue
        ];
        $response = $this->graphQlMutation($query, $variables);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertEquals('Delivery date was changed.', $response[self::MAIN_QUERY_KEY]['response']);

        $this->assertArrayHasKey(self::CART_KEY, $response[self::MAIN_QUERY_KEY]);
        $this->assertArrayHasKey(self::ADDIT_QUERY_KEY, $response[self::MAIN_QUERY_KEY][self::CART_KEY]);
        $this->assertResponseFields(
            $response[self::MAIN_QUERY_KEY][self::CART_KEY][self::ADDIT_QUERY_KEY],
            $assertDeliveryValues
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
    $comment: String,
    $date: String!,
    $time: Int
  ) {
  updateDeliveryInformation(
    input: {
      cart_id:$cartId
      comment:$comment
      date:$date
      time:$time
    }
  ){
    response
    cart {
      amasty_delivery_date {
        comment
        date
        time {
          displayValue
          value
        }
      }
    }
  }
}
MUTATION;
    }
}
