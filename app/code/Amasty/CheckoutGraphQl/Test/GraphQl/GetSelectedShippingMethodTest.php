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

class GetSelectedShippingMethodTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'getSelectedShippingMethod';

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
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/set_flatrate_shipping_method.php
     */
    public function testGetSelectedShippingMethod()
    {
        $fields = [
            'carrier_code' => 'flatrate',
            'method_code' => 'flatrate'
        ];

        $maskedQuote = $this->getMaskedQuoteIdByReservedOrderId->execute('test_quote');
        $query = $this->getQuery($maskedQuote);
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_QUERY_KEY], $fields);
    }

    /**
     * @param string $maskedQuoteId
     * @return string
     */
    private function getQuery(string $maskedQuoteId): string
    {
        return <<<QUERY
query {
  getSelectedShippingMethod(cartId: "$maskedQuoteId")
  {
     carrier_code
     method_code
  }
}
QUERY;
    }
}
