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

class GetAdditionalFieldsTest extends GraphQlAbstract
{
    public const CUSTOMER_NAME = 'customer@example.com';
    public const CUSTOMER_PASS = 'password';
    public const MAIN_QUERY_KEY = 'getAdditionalFields';

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
     * @magentoApiDataFixture Magento/GraphQl/Catalog/_files/simple_product.php
     * @magentoApiDataFixture Amasty_CheckoutGraphQl::Test/GraphQl/_files/customer/create_empty_cart_additional.php
     * @magentoApiDataFixture Magento/GraphQl/Quote/_files/add_simple_product.php
     */
    public function testGetAdditionalFields()
    {
        $maskedQuote = $this->getMaskedQuoteIdByReservedOrderId->execute('test_quote');
        $query = $this->getQuery($maskedQuote);
        $response = $this->graphQlQuery($query, [], '', $this->getHeader());

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertEquals('test_comment', $response[self::MAIN_QUERY_KEY]['comment']);
        $this->assertTrue($response[self::MAIN_QUERY_KEY]['is_register']);
        $this->assertFalse($response[self::MAIN_QUERY_KEY]['is_subscribe']);
        $this->assertEquals('05/12/2023', $response[self::MAIN_QUERY_KEY]['register_dob']);
    }

    private function getQuery(string $maskedQuoteId): string
    {
        return <<<QUERY
query{
  getAdditionalFields(cartId: "$maskedQuoteId")
  {
    comment
    is_register
    is_subscribe
    register_dob
  }
}
QUERY;
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
