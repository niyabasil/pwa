<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Search;

use Magento\CatalogSearch\Helper\Data as HelperData;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Search\Model\Query;
use Magento\Search\Model\QueryFactory;

/**
 * Save terms log
 */
class SaveTermLog implements ResolverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * Catalog search helper
     *
     * @var HelperData
     */
    private $catalogSearchHelper;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @param HelperData $catalogSearchHelper
     * @param QueryFactory $queryFactory
     * @param RequestInterface $request
     */
    public function __construct(
        HelperData $catalogSearchHelper,
        QueryFactory $queryFactory,
        RequestInterface $request
    ) {
        $this->catalogSearchHelper = $catalogSearchHelper;
        $this->queryFactory = $queryFactory;
        $this->_request = $request;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args['query'])) {
            throw new GraphQlInputException(__('Specify the "query" value.'));
        }

        $this->_request->setParam('q', $args['query']);
        /* @var $query Query */
        $query = $this->queryFactory->get();
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $query->setStoreId($storeId);
        if ($query->getQueryText() != '') {
            try {
                $numResults = isset($args['num_results']) ? (int)$args['num_results'] : 0;
                $query->setNumResults($numResults);
                $query->save();
                if ($this->catalogSearchHelper->isMinQueryLength()) {
                    $query->setId(0)->setIsActive(1)->setIsProcessed(1);
                } else {
                    $query->saveIncrementalPopularity();
                }
                return true;
            } catch (LocalizedException $e) {
                return false;
            }
        }

        return false;
    }
}
