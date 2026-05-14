<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\CmsUrlRewriteGraphQl\Model\DataProvider\UrlRewrite;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class Page
 * @package Tigren\Pwa\Plugin\Magento\CmsUrlRewriteGraphQl\Model\DataProvider\UrlRewrite
 */
class Page
{
    /**
     * @param \Magento\CmsUrlRewriteGraphQl\Model\DataProvider\UrlRewrite\Page $subject
     * @param $result
     * @param string $entity_type
     * @param int $id
     * @param ResolveInfo|null $info
     * @param int|null $storeId
     * @return array
     */
    public function afterGetData(
        \Magento\CmsUrlRewriteGraphQl\Model\DataProvider\UrlRewrite\Page $subject,
        $result,
        string $entity_type,
        int $id,
        ResolveInfo $info = null,
        int $storeId = null
    ): array {
        if (is_array($result) && !isset($result['id']) && isset($result['page_id'])) {
            $result['id'] = $result['page_id'];
        }

        return $result;
    }
}
