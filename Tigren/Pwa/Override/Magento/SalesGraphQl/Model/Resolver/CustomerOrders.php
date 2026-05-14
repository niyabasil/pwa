<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Override\Magento\SalesGraphQl\Model\Resolver;

use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\SalesGraphQl\Model\Formatter\Order as OrderFormatter;
use Magento\SalesGraphQl\Model\Resolver\CustomerOrders\Query\OrderFilter;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Orders data resolver
 */
class CustomerOrders extends \Magento\SalesGraphQl\Model\Resolver\CustomerOrders
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderFilter
     */
    private $orderFilter;

    /**
     * @var OrderFormatter
     */
    private $orderFormatter;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderFilter $orderFilter
     * @param OrderFormatter $orderFormatter
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderFilter $orderFilter,
        OrderFormatter $orderFormatter,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderFilter = $orderFilter;
        $this->orderFormatter = $orderFormatter;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        $storeIds = [];
        $userId = $context->getUserId();
        /** @var StoreInterface $store */
        $store = $context->getExtensionAttributes()->getStore();
        if (isset($args['scope'])) {
            $storeIds = $this->getStoresByScope($args['scope'], $store);
        }
        try {
            $searchResult = $this->getSearchResult($args, (int)$userId, (int)$store->getId(), $storeIds);
            $maxPages = (int)ceil($searchResult->getTotalCount() / $searchResult->getPageSize());
        } catch (InputException $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        $ordersArray = [];
        foreach ($searchResult->getItems() as $orderModel) {
            $ordersArray[] = $this->orderFormatter->format($orderModel);
        }

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $ordersArray,
            'page_info' => [
                'page_size' => $searchResult->getPageSize(),
                'current_page' => $searchResult->getCurPage(),
                'total_pages' => $maxPages,
            ]
        ];
    }

    /**
     * Get search result from graphql query arguments
     *
     * @param array $args
     * @param int $userId
     * @param int $storeId
     * @return OrderSearchResultInterface
     * @throws InputException
     */
    private function getSearchResult(array $args, int $userId, int $storeId, $storeIds)
    {
        $filterGroups = $this->orderFilter->createFilterGroups($args, $userId, (int)$storeId, $storeIds);
        $this->searchCriteriaBuilder->setFilterGroups($filterGroups);
        $this->addEntityIdSort();
        if (isset($args['currentPage'])) {
            $this->searchCriteriaBuilder->setCurrentPage($args['currentPage']);
        }
        if (isset($args['pageSize'])) {
            $this->searchCriteriaBuilder->setPageSize($args['pageSize']);
        }
        return $this->orderRepository->getList($this->searchCriteriaBuilder->create());
    }

    /**
     * Add sort by Entity ID
     *
     */
    private function addEntityIdSort(): void
    {
        $this->searchCriteriaBuilder->addSortOrder(
            $this->sortOrderBuilder
                ->setField('created_at')
                ->setDirection(SortOrder::SORT_DESC)
                ->create()
        );
    }
}
