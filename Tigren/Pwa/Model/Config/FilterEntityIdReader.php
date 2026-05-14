<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Config;

use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\GraphQl\Schema\Type\Entity\MapperInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

/**
 * Adds custom/eav attributes to product filter type in the GraphQL config.
 *
 * Product Attribute should satisfy the following criteria:
 * - (Attribute is searchable AND "Visible in Advanced Search" is set to "Yes")
 * - OR attribute is "Used in Layered Navigation"
 * - AND Attribute of type "Select" must have options
 */
class FilterEntityIdReader implements ReaderInterface
{
    /**
     * Entity type constant
     */
    private const ENTITY_TYPE = 'filter_attributes';

    /**
     * Filter input types
     */
    private const FILTER_EQUAL_TYPE = 'FilterEqualTypeInput';

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @param MapperInterface $mapper
     */
    public function __construct(
        MapperInterface $mapper
    ) {
        $this->mapper = $mapper;
    }

    /**
     * Read configuration scope
     *
     * @param string|null $scope
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function read($scope = null): array
    {
        $typeNames = $this->mapper->getMappedTypes(self::ENTITY_TYPE);
        $config = [];
        $idField = 'entity_id';
        foreach ($typeNames as $typeName) {
            $config[$typeName]['fields'][$idField] = [
                'name' => $idField,
                'type' => self::FILTER_EQUAL_TYPE,
                'arguments' => [],
                'required' => false,
                'description' => 'Entity ID'
            ];
        }

        return $config;
    }
}
