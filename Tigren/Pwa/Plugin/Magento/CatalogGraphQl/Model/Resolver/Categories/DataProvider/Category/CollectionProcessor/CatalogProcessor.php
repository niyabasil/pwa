<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CatalogGraphQl\Model\Resolver\Categories\DataProvider\Category\CollectionProcessor;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DB\Select;
use Zend_Db_Expr;

/**
 * Class CatalogProcessor
 * @package Tigren\Pwa\Plugin\Magento\CatalogGraphQl\Model\Resolver\Categories\DataProvider\Category\CollectionProcessor
 */
class CatalogProcessor
{
    /**
     * @param \Magento\CatalogGraphQl\Model\Resolver\Categories\DataProvider\Category\CollectionProcessor\CatalogProcessor $subject
     * @param $result
     * @param $collection
     * @param SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function afterProcess(
        \Magento\CatalogGraphQl\Model\Resolver\Categories\DataProvider\Category\CollectionProcessor\CatalogProcessor $subject,
        $result,
        $collection,
        SearchCriteriaInterface $searchCriteria
    ) {
        if ($result->getItemObjectClass() === 'Magento\Catalog\Model\Category') {
            foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
                $filters = $filterGroup->getFilters();
                foreach ($filters as $filter) {
                    if ($filter->getField() === 'entity_id' && $filter->getConditionType() === 'in' && is_array($filter->getValue())) {
                        $orderList = join(',', $filter->getValue());
                        $result->getSelect()
                            ->reset(Select::ORDER)
                            ->order(new Zend_Db_Expr("FIELD(e.entity_id,$orderList)"));
                    }
                }
            }
        }

        return $result;
    }
}
