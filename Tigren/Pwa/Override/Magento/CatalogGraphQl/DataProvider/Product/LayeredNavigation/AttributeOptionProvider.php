<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Override\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Store\Model\Store;
use Zend_Db_Statement_Exception;

/**
 * Class AttributeOptionProvider
 */
class AttributeOptionProvider extends \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\AttributeOptionProvider
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        parent::__construct($resourceConnection);
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Get option data. Return list of attributes with option data
     *
     * @param array $optionIds
     * @param int|null $storeId
     * @param array $attributeCodes
     * @return array
     * @throws Zend_Db_Statement_Exception
     */
    public function getOptions(array $optionIds, ?int $storeId, array $attributeCodes = []): array
    {
        if (!$optionIds) {
            return [];
        }

        $storeId = $storeId ?: Store::DEFAULT_STORE_ID;
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['a' => $this->resourceConnection->getTableName('eav_attribute')],
                [
                    'attribute_id' => 'a.attribute_id',
                    'attribute_code' => 'a.attribute_code',
                    'attribute_label' => 'a.frontend_label',
                ]
            )
            ->joinLeft(
                ['attribute_label' => $this->resourceConnection->getTableName('eav_attribute_label')],
                "a.attribute_id = attribute_label.attribute_id AND attribute_label.store_id = {$storeId}",
                [
                    'attribute_store_label' => 'attribute_label.value',
                ]
            )
            ->joinLeft(
                ['options' => $this->resourceConnection->getTableName('eav_attribute_option')],
                'a.attribute_id = options.attribute_id',
                []
            )
            ->joinLeft(
                ['option_value' => $this->resourceConnection->getTableName('eav_attribute_option_value')],
                'options.option_id = option_value.option_id',
                [
                    'option_id' => 'option_value.option_id',
                ]
            )->joinLeft(
                ['option_value_store' => $this->resourceConnection->getTableName('eav_attribute_option_value')],
                "options.option_id = option_value_store.option_id AND option_value_store.store_id = {$storeId}",
                [
                    'option_label' => $connection->getCheckSql(
                        'option_value_store.value_id > 0',
                        'option_value_store.value',
                        'option_value.value'
                    )
                ]
            )->where(
                'a.attribute_id = options.attribute_id AND option_value.store_id = ?',
                Store::DEFAULT_STORE_ID
            );

        $select->where('option_value.option_id IN (?)', $optionIds);

        if (!empty($attributeCodes)) {
            $select->orWhere(
                'a.attribute_code in (?) AND a.frontend_input = \'boolean\'',
                $attributeCodes
            );
        }
        $select->order('options.sort_order ASC')->order('option_value.value');
        return $this->formatResult($select);
    }

    /**
     * Format result
     *
     * @param Select $select
     * @return array
     * @throws Zend_Db_Statement_Exception
     */
    private function formatResult(Select $select): array
    {
        $statement = $this->resourceConnection->getConnection()->query($select);

        $result = [];
        while ($option = $statement->fetch()) {
            if (!isset($result[$option['attribute_code']])) {
                $result[$option['attribute_code']] = [
                    'attribute_id' => $option['attribute_id'],
                    'attribute_code' => $option['attribute_code'],
                    'attribute_label' => $option['attribute_store_label']
                        ? $option['attribute_store_label'] : $option['attribute_label'],
                    'options' => [],
                ];
            }
            $result[$option['attribute_code']]['options'][$option['option_id']] = $option['option_label'];
        }

        return $result;
    }
}
