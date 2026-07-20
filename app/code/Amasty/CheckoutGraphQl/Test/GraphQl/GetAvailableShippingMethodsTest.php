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

class GetAvailableShippingMethodsTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'getAvailableShippingMethods';
    public const RESPONSE_KEY = 'available_shipping_methods';

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
     * @magentoConfigFixture base_website carriers/flatrate/active 1
     * @magentoConfigFixture base_website carriers/tablerate/active 1
     *
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/guest/create_empty_cart.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/add_simple_product.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/set_new_shipping_address.php
     */
    public function testGetAvailableShippingMethods()
    {
        $fields = [
            [
                'carrier_code' => 'flatrate',
                'method_code' => 'flatrate'
            ],
            [
                'carrier_code' => 'tablerate',
                'method_code' => 'bestway'
            ]
        ];

        $maskedQuote = $this->getMaskedQuoteIdByReservedOrderId->execute('test_quote');
        $query = $this->getQuery($maskedQuote);
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertArrayHasKey(self::RESPONSE_KEY, $response[self::MAIN_QUERY_KEY]);
        $this->assertResponseFields(json_decode($response[self::MAIN_QUERY_KEY][ self::RESPONSE_KEY], true), $fields);
    }

    /**
     * @param string $maskedQuoteId
     * @return string
     */
    private function getQuery(string $maskedQuoteId): string
    {
        return <<<QUERY
query{
  getAvailableShippingMethods(cartId: "$maskedQuoteId")
  {
    available_shipping_methods
  }
}
QUERY;
    }
}
