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
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class EcssoBanners implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    private $ruleCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Uid
     */
    private $uid;

    /**
     * @var Timezone
     */
    private $timezone;

    public function __construct(
        CollectionFactory $ruleCollectionFactory,
        StoreManagerInterface $storeManager,
        Uid $uid,
        Timezone $timezone
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->storeManager = $storeManager;
        $this->uid = $uid;
        $this->timezone = $timezone;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $pageType    = isset($args['page_type']) ? strtoupper($args['page_type']) : 'HOME';
        $categoryUid = $args['category_id'] ?? null;
        $productSku  = $args['product_sku'] ?? null;
        $posId       = $args['pos_id'] ?? null;

        // Decode base64-encoded category UID to a plain integer ID.
        $categoryId = $categoryUid ? (int)$this->uid->decode($categoryUid) : 0;

        // Customer group: use GraphQL context when available, fall back to NOT_LOGGED_IN (0).
        $customerGroupId = 0;
        if ($context && method_exists($context, 'getExtensionAttributes')) {
            $ext = $context->getExtensionAttributes();
            if ($ext && method_exists($ext, 'getCustomerGroupId')) {
                $customerGroupId = (int)$ext->getCustomerGroupId();
            }
        }

        $storeId     = (int)$this->storeManager->getStore()->getId();
        $mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        // Plain Y-m-d H:i:s string so MySQL can compare against stored date values.
        $currentTime = $this->timezone->date()->format('Y-m-d H:i:s');

        /** @var \Amasty\PromoBanners\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();
        $collection
            // from_date <= now  → banner has already started
            ->addFieldToFilter('from_date', [['null' => true], ['lteq' => $currentTime]])
            // to_date   >= now  → banner has not yet expired
            ->addFieldToFilter('to_date',   [['null' => true], ['gteq' => $currentTime]])
            ->addFieldToFilter('cust_groups', ['', ['finset' => $customerGroupId]])
            ->addFieldToFilter('stores',      ['', ['finset' => $storeId]])
            ->addFieldToFilter('is_active', 1)
            ->setOrder('sort_order', 'desc');

        // Filter by explicit position ID when provided.
        if ($posId !== null) {
            $collection->addFieldToFilter('banner_position', ['finset' => $posId]);
        }

        // Filter by category on category pages.
        if ($categoryId && $pageType === 'CATEGORY') {
            $collection->addFieldToFilter('cats', ['finset' => $categoryId]);
        }

        // Filter by product SKU on product pages.
        if ($productSku && $pageType === 'PRODUCT') {
            $collection->addFieldToFilter('show_on_products', ['finset' => $productSku]);
        }

        $items = [];

        /** @var Rule $banner */
        foreach ($collection->getItems() as $banner) {
            $bannerImg = null;
            if ($banner->getBannerType() === Rule::TYPE_IMAGE && $banner->getBannerImg()) {
                $bannerImg = $mediaBaseUrl . 'amasty/ampromobanners/' . $banner->getBannerImg();
            }

            $items[] = [
                'id'          => (int)$banner->getId(),
                'title'       => (string)$banner->getBannerTitle(),
                'subtitle'    => (string)$banner->getRuleName(),
                'image'       => $bannerImg,
                'button_text' => (string)$banner->getRuleName(),
                'button_link' => (string)$banner->getBannerLink(),
            ];
        }

        return $items;
    }
}
