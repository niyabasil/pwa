<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners GraphQL for Magento 2
 */

declare(strict_types=1);

namespace Amasty\PromoBannersGraphQl\Model\Resolver;

use Amasty\PromoBanners\Model\ResourceModel\Rule\CollectionFactory;
use Amasty\PromoBanners\Model\Rule;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Banners implements ResolverInterface
{
    /**
     * Position constants → which page types show them by default.
     * Positions that require explicit page context are listed per page type below.
     */
    private const PAGE_POSITIONS = [
        'HOME'     => [Rule::POS_TOP_INDEX, Rule::POS_TOP_PAGE],
        'CATEGORY' => [Rule::POS_CATEGORY_PAGE, Rule::POS_CATEGORY_PAGE_BOTTOM,
                       Rule::POS_CATEGORY_PAGE_BELOW_ADD_TO_CART, Rule::POS_AMONG_PRODUCTS,
                       Rule::POS_SIDEBAR_LEFT, Rule::POS_SIDEBAR_RIGHT, Rule::POS_TOP_PAGE],
        'PRODUCT'  => [Rule::POS_PROD_PAGE, Rule::POS_PROD_PAGE_BOTTOM, Rule::POS_PROD_PAGE_BELOW_CART,
                       Rule::POS_PROD_PAGE_RIGHT, Rule::POS_PROD_PAGE_LEFT, Rule::POS_TOP_PAGE],
        'CART'     => [Rule::POS_ABOVE_CART, Rule::POS_CHECKOUT_BELOW_TOTAL, Rule::POS_TOP_PAGE],
        'SEARCH'   => [Rule::POS_CATALOG_SEARCH_TOP, Rule::POS_TOP_PAGE],
    ];

    /**
     * @var CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Timezone
     */
    private $timezone;

    public function __construct(
        CollectionFactory $ruleCollectionFactory,
        StoreManagerInterface $storeManager,
        Timezone $timezone
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $pageType    = isset($args['page_type']) ? strtoupper($args['page_type']) : null;
        $categoryId  = $args['category_id'] ?? null;
        $productSku  = $args['product_sku'] ?? null;
        $searchQuery = $args['search_query'] ?? null;

        // Customer group: use context when available, fall back to NOT_LOGGED_IN (0).
        $customerGroupId = 0;
        if ($context && method_exists($context, 'getExtensionAttributes')) {
            $ext = $context->getExtensionAttributes();
            if ($ext && method_exists($ext, 'getCustomerGroupId')) {
                $customerGroupId = (int)$ext->getCustomerGroupId();
            }
        }

        $storeId     = (int)$this->storeManager->getStore()->getId();
        $currentTime = $this->timezone->date();

        /** @var \Amasty\PromoBanners\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();
        $collection
            ->addFieldToFilter('from_date', [['null' => true], ['lteq' => $currentTime]])
            ->addFieldToFilter('to_date',   [['null' => true], ['gteq' => $currentTime]])
            ->addFieldToFilter('cust_groups', ['', ['finset' => $customerGroupId]])
            ->addFieldToFilter('stores',      ['', ['finset' => $storeId]])
            ->addFieldToFilter('is_active', 1)
            ->setOrder('sort_order', 'desc');

        // Filter by category when provided
        if ($categoryId !== null) {
            $collection->addFieldToFilter(
                'cats',
                [['eq' => ''], ['finset' => $categoryId]]
            );
        }

        // Filter by product SKU when provided
        if ($productSku !== null) {
            $collection->addFieldToFilter(
                'show_on_products',
                [['null' => true], ['finset' => $productSku]]
            );
        }

        $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $allowedPositions = $pageType ? (self::PAGE_POSITIONS[$pageType] ?? null) : null;

        $items = [];
        /** @var Rule $banner */
        foreach ($collection->getItems() as $banner) {
            // Validate search-term restriction when a search query is given
            if ($searchQuery !== null && !$banner->validateSearch($searchQuery)) {
                continue;
            }

            $positions = $banner->getBannerPositions();
            $positionsInt = array_map('intval', $positions);

            // When a page type is supplied keep only banners that have at least
            // one position matching that page type.
            if ($allowedPositions !== null) {
                $matching = array_intersect($positionsInt, $allowedPositions);
                if (empty($matching) && !empty($positionsInt)) {
                    continue;
                }
            }

            // Resolve banner image URL for image-type banners
            $bannerImg = null;
            if ($banner->getBannerType() === Rule::TYPE_IMAGE && $banner->getBannerImg()) {
                $bannerImg = $mediaBaseUrl . 'amasty/ampromobanners/' . $banner->getBannerImg();
            }

            $items[] = [
                'banner_id'   => (int)$banner->getId(),
                'rule_name'   => $banner->getRuleName(),
                'banner_type' => $banner->getBannerType(),
                'banner_img'  => $bannerImg,
                'banner_link' => $banner->getBannerLink() ?: null,
                'banner_title'=> $banner->getBannerTitle() ?: null,
                'html_text'   => $banner->getBannerType() === Rule::TYPE_HTML
                                    ? $banner->getHtmlText()
                                    : null,
                'cms_block'   => $banner->getBannerType() === Rule::TYPE_CMS
                                    ? $banner->getCmsBlock()
                                    : null,
                'positions'   => $positionsInt,
                'sort_order'  => (int)$banner->getSortOrder(),
            ];
        }

        return ['items' => $items];
    }
}
