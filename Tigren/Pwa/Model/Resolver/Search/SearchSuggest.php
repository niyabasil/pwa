<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Search;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Search\Model\QueryFactory;

/**
 * Class SearchSuggest
 * @package Tigren\Pwa\Model\Resolver\Search
 */
class SearchSuggest implements ResolverInterface
{
    /**
     * @var  AutocompleteInterface
     */
    private $autocomplete;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param AutocompleteInterface $autocomplete
     * @param RequestInterface $request
     */
    public function __construct(
        AutocompleteInterface $autocomplete,
        RequestInterface $request
    ) {
        $this->autocomplete = $autocomplete;
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
        $autocompleteData = $this->autocomplete->getItems();
        $responseData = [];
        foreach ($autocompleteData as $resultItem) {
            $responseData[] = $resultItem->toArray();
        }
        return $responseData;
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
    }
}
