<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Country;

use Magento\Directory\Model\CountryFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Country
 * @package Tigren\Pwa\Model\Resolver
 */
class CountryName implements ResolverInterface
{
    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        CountryFactory $countryFactory
    ) {
        $this->countryFactory = $countryFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $countryCode = isset($value['country_code']) ? $value['country_code'] : null;
        if (empty($countryCode)) {
            return null;
        }

        $country = $this->countryFactory->create();

        return $country->loadByCode($countryCode)->getName();
    }
}
