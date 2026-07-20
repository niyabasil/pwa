<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package One Step Checkout GraphQL (System)
 */

namespace Amasty\CheckoutGraphQl\Test\GraphQl;

use Magento\TestFramework\TestCase\GraphQlAbstract;

class GetDefaultIpDataTest extends GraphQlAbstract
{
    public const MAIN_QUERY_KEY = 'getDefaultIpData';

    /**
     * @group amasty_osc
     *
     * @magentoConfigFixture default_store amasty_checkout/default_values/address_country_id US
     * @magentoConfigFixture default_store amasty_checkout/default_values/address_region_id 12
     * @magentoConfigFixture default_store amasty_checkout/default_values/address_postcode 95131
     * @magentoConfigFixture default_store amasty_checkout/default_values/address_city Alameda
     */
    public function testGetDefaultIpData()
    {
        $expectedResponse = [
            "country_id" => "US",
            "region_id" => "12",
            "city" => "Alameda",
            "postcode" => "95131"
        ];

        $query = $this->getQuery();
        $response = $this->graphQlQuery($query);

        $this->assertArrayHasKey(self::MAIN_QUERY_KEY, $response);
        $this->assertResponseFields($response[self::MAIN_QUERY_KEY], $expectedResponse);
        $this->assertNull($response[self::MAIN_QUERY_KEY]['region']);
    }

    /**
     * @return string
     */
    private function getQuery(): string
    {
        return <<<QUERY
query{
   getDefaultIpData{
     country_id
     region
     region_id
     city
     postcode
  }
}
QUERY;
    }
}
