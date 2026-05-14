<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Sales;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\SalesGraphQl\Model\Formatter\Order as OrderFormatter;
use function current;

/**
 * Retrieves the Billing information object
 */
class Order implements ResolverInterface
{
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderFormatter
     */
    private $orderFormatter;

    /**
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderFormatter $orderFormatter
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderFormatter $orderFormatter
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderFormatter = $orderFormatter;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->validateArgs($args);
        $searchResult = $this->getSearchResult($args);

        if ($searchResult->getTotalCount() === 0) {
            throw new GraphQlInputException(__("The requested order was not found. Please try again."));
        }

        /** @var OrderInterface $order */
        $order = current($searchResult->getItems());

        if (
            (!empty($args['tracking']))
            && (
                ($args['tracking']["oar_type"] == 'email' && $args['tracking']['oar_email'] != $order->getBillingAddress()->getEmail())
                || ($args['tracking']["oar_type"] == 'zip' && $args['tracking']['oar_zip'] != $order->getBillingAddress()->getPostcode())
            )
        ) {
            throw new GraphQlInputException(__("The order you find is not your order"));
        }

        return $this->orderFormatter->format($order);
    }

    /**
     * @throws GraphQlInputException
     */
    private function validateArgs($args)
    {
        if (empty($args["order_number"])) {
            throw new GraphQlInputException(__("`order_number` should be required"));
        }
        
        if (!empty($args['tracking'])) {
            $this->validateTracking($args['tracking']);
        }
    }

    /**
     * @param $args
     * @throws GraphQlInputException
     */
    private function validateTracking($args)
    {
        if (empty($args["oar_billing_lastname"])) {
            throw new GraphQlInputException(__("`oar_billing_lastname` should be required"));
        }
        if (empty($args["oar_type"])) {
            throw new GraphQlInputException(__("`oar_type` should be required"));
        }
        if ($args["oar_type"] == 'email' && empty($args['oar_email'])) {
            throw new GraphQlInputException(__("`oar_email` should be required"));
        }
        if ($args["oar_type"] == 'zip' && empty($args['oar_zip'])) {
            throw new GraphQlInputException(__("`oar_zip` should be required"));
        }
    }

    /**
     * Get search result from graphql query arguments
     *
     * @param array $args
     * @return OrderSearchResultInterface
     */
    private function getSearchResult(array $args)
    {
        $this->filterGroupBuilder->setFilters(
            [$this->filterBuilder->setField('increment_id')->setValue($args['order_number'])->setConditionType('eq')->create()]
        );
        $this->searchCriteriaBuilder->setFilterGroups([$this->filterGroupBuilder->create()]);
        return $this->orderRepository->getList($this->searchCriteriaBuilder->create());
    }
}
