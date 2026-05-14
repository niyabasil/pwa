<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\GraphQlCache\Model;

use Magento\Framework\App\RequestInterface;

/**
 * Add category filter inputs request to cache
 */
class CacheableQuery
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\GraphQlCache\Model\CacheableQuery $subject
     */
    public function beforeShouldPopulateCacheHeadersWithTags(
        \Magento\GraphQlCache\Model\CacheableQuery $subject
    ) {
        $requestParams = $this->request->getParams();
        if (!empty($requestParams['operationName']) && $requestParams['operationName'] === 'GetFilterInputsForCategory') {
            $subject->setCacheValidity(true);
            $subject->addCacheTags(['category_filter_inputs']);
        }
    }
}
