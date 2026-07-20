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

class SaveAdditionalFieldsTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'saveAdditionalFields';

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
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/guest/create_empty_cart.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/add_simple_product.php
     */
    public function testSaveAdditionalFields()
    {
        $maskedQuote = $this->getMaskedQuoteIdByReservedOrderId->execute('test_quote');
        $query = $this->getMutation();
        $variables =  [
            'cartId' => $maskedQuote,
            'comment' => 'test comment',
            'isRegister' => false,
            'isSubscribe' => true,
            'registerDob' => '05/13/2023'
        ];
        $response = $this->graphQlMutation($query, $variables);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertEquals('Additional fields were saved.', $response[self::MAIN_QUERY_KEY]['response']);
    }

    /**
     * @return string
     */
    private function getMutation(): string
    {
        return <<<'MUTATION'
mutation SaveAdditionalFields(
    $cartId: String!,
    $comment: String,
    $isRegister: Boolean,
    $isSubscribe: Boolean,
    $registerDob: String
  ) {
        saveAdditionalFields (
            input: {
                cart_id:$cartId
                comment:$comment
                is_register:$isRegister
                is_subscribe:$isSubscribe
                register_dob:$registerDob
            }
        ) {
            response
        }
    }
MUTATION;
    }
}
