<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Search;

use Magento\AdvancedSearch\Model\Recommendations\DataProvider;
use Magento\AdvancedSearch\Model\SuggestedQueries;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Search\Model\QueryFactory;
use Magento\Search\Model\QueryFactoryInterface;
use Magento\Search\Model\QueryInterface;

/**
 * Class SearchDetail
 * @package Tigren\Pwa\Model\Resolver\Search
 */
class SearchDetail implements ResolverInterface
{
    /**
     * @var  DataProvider
     */
    private $recommendDataProvider;

    /**
     * @var  SuggestedQueries
     */
    private $suggestDataProvider;

    /**
     * @var QueryInterface
     */
    private $query;

    /**
     * @var QueryFactoryInterface
     */
    private $queryFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param DataProvider $recommendDataProvider
     * @param SuggestedQueries $suggestDataProvider
     * @param QueryFactoryInterface $queryFactory
     * @param RequestInterface $request
     */
    public function __construct(
        DataProvider $recommendDataProvider,
        SuggestedQueries $suggestDataProvider,
        QueryFactoryInterface $queryFactory,
        RequestInterface $request
    ) {
        $this->recommendDataProvider = $recommendDataProvider;
        $this->suggestDataProvider = $suggestDataProvider;
        $this->queryFactory = $queryFactory;
        $this->request = $request;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return bool|Value|mixed
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->initQuery($args);

        return [
            'suggestions' => [
                'items' => $this->formatItemsData($this->suggestDataProvider->getItems($this->query)),
                'show_result_count' => $this->suggestDataProvider->isResultsCountEnabled(),
            ],
            'recommendations' => [
                'items' => $this->formatItemsData($this->recommendDataProvider->getItems($this->query)),
                'show_result_count' => $this->recommendDataProvider->isResultsCountEnabled(),
            ]
        ];
    }

    /**
     * @param $args
     * @throws GraphQlInputException
     */
    protected function initQuery($args)
    {
        if (!isset($args['query'])) {
            throw new GraphQlInputException(__('Specify the "query" value.'));
        }
        $this->request->setParam(QueryFactory::QUERY_VAR_NAME, $args['query']);
        $this->query = $this->queryFactory->get();
    }

    /**
     * @param array $items
     * @return array
     */
    protected function formatItemsData($items = [])
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = [
                'queryText' => $item->getQueryText(),
                'resultsCount' => (int)$item->getResultsCount(),
            ];
        }
        return $result;
    }
}
