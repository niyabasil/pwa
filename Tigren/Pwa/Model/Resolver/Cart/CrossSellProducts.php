<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Model\Resolver\Cart;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\LinkFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\RelatedProducts;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CrossSellProducts
 * @package Tigren\Pwa\Model\Resolver\Cart
 */
class CrossSellProducts implements ResolverInterface
{
    /**
     * Items quantity will be capped to this value
     *
     * @var int
     */
    protected $_maxItemCount = 12;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Visibility
     */
    protected $_productVisibility;

    /**
     * @var StockHelper
     */
    protected $stockHelper;

    /**
     * @var LinkFactory
     */
    protected $_productLinkFactory;

    /**
     * @var RelatedProducts
     */
    protected $_itemRelationsList;

    /**
     * @var CollectionFactory|null
     */
    private $productCollectionFactory;

    /**
     * @var ProductRepositoryInterface|null
     */
    private $productRepository;

    /**
     * Catalog config
     *
     * @var Config
     */
    private $_catalogConfig;

    /**
     * @var Product[]
     */
    private $cartProducts;

    /**
     * @var CartInterface
     */
    private $_quote;

    /**
     * @var int
     */
    private $_lastAddedProductId;

    /**
     * @param StoreManagerInterface $storeManager
     * @param Visibility $productVisibility
     * @param LinkFactory $productLinkFactory
     * @param RelatedProducts $itemRelationsList
     * @param StockHelper $stockHelper
     * @param Config $catalogConfig
     * @param CollectionFactory|null $productCollectionFactory
     * @param ProductRepositoryInterface|null $productRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Visibility $productVisibility,
        LinkFactory $productLinkFactory,
        RelatedProducts $itemRelationsList,
        StockHelper $stockHelper,
        Config $catalogConfig,
        ?CollectionFactory $productCollectionFactory = null,
        ?ProductRepositoryInterface $productRepository = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_productVisibility = $productVisibility;
        $this->_productLinkFactory = $productLinkFactory;
        $this->_itemRelationsList = $itemRelationsList;
        $this->stockHelper = $stockHelper;
        $this->_catalogConfig = $catalogConfig;
        $this->productCollectionFactory = $productCollectionFactory
            ?? ObjectManager::getInstance()->get(CollectionFactory::class);
        $this->productRepository = $productRepository
            ?? ObjectManager::getInstance()->get(ProductRepositoryInterface::class);
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            return null;
        }

        $this->setQuote($value['model']);
        $items = [];

        $ninProductIds = $this->_getCartProductIds();
        if ($ninProductIds) {
            $lastAddedProduct = $this->getLastAddedProduct();
            if ($lastAddedProduct) {
                $collection = $this->_getCollection()
                    ->addProductFilter($lastAddedProduct->getData($this->getProductLinkField()));
                if (!empty($ninProductIds)) {
                    $collection->addExcludeProductFilter($ninProductIds);
                }
                $collection->setPositionOrder()->load();
                $collection->addOptionsToResult();
                foreach ($collection as $item) {
                    $ninProductIds[] = $item->getId();
                    $itemData = $item->getData();
                    $itemData['model'] = $item;
                    $items[] = $itemData;
                }
            }

            if (count($items) < $this->_maxItemCount) {
                $filterProductIds = array_merge(
                    $this->getCartProductLinkIds(),
                    $this->getCartRelatedProductLinkIds()
                );
                $collection = $this->_getCollection()->addProductFilter(
                    $filterProductIds
                )->addExcludeProductFilter(
                    $ninProductIds
                )->setPageSize(
                    $this->_maxItemCount - count($items)
                )->setGroupBy()->setPositionOrder()->load();
                $collection->addOptionsToResult();
                foreach ($collection as $item) {
                    $itemData = $item->getData();
                    $itemData['model'] = $item;
                    $items[] = $itemData;
                }
            }
        }

        return $items;
    }

    /**
     * Get ids of products that are in cart
     *
     * @return array
     */
    protected function _getCartProductIds()
    {
        $ids = [];

        foreach ($this->getCartProducts() as $product) {
            $ids[] = $product->getId();
        }

        return $ids;
    }

    /**
     * Get quote instance
     *
     * @return CartInterface
     * @codeCoverageIgnore
     */
    public function setQuote($cart)
    {
        if (!isset($this->_quote)) {
            $this->_quote = $cart;
        }
        return $this->_quote;
    }

    /**
     * Get quote instance
     *
     * @return CartInterface
     * @codeCoverageIgnore
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Get crosssell products collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     * @throws NoSuchEntityException
     */
    protected function _getCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection */
        $collection = $this->_productLinkFactory->create()->useCrossSellLinks()->getProductCollection()->setStoreId(
            $this->_storeManager->getStore()->getId()
        )->addStoreFilter()->setPageSize(
            $this->_maxItemCount
        )->setVisibility(
            $this->_productVisibility->getVisibleInCatalogIds()
        );
        $this->_addProductAttributesAndPrices($collection);

        return $collection;
    }

    /**
     * Get product link ID field
     *
     * @return string
     */
    private function getProductLinkField(): string
    {
        /* @var $collection Collection */
        $collection = $this->productCollectionFactory->create();
        return $collection->getProductEntityMetadata()->getLinkField();
    }

    /**
     * Get cart products link IDs
     *
     * @return array
     */
    private function getCartProductLinkIds(): array
    {
        $linkField = $this->getProductLinkField();
        $linkIds = [];
        foreach ($this->getCartProducts() as $product) {
            /** * @var Product $product */
            $linkIds[] = $product->getData($linkField);
        }
        return $linkIds;
    }

    /**
     * Get cart related products link IDs
     *
     * @return array
     */
    private function getCartRelatedProductLinkIds(): array
    {
        $productIds = $this->_itemRelationsList->getRelatedProductIds($this->getQuote()->getAllItems());
        $linkIds = [];
        if (!empty($productIds)) {
            $linkField = $this->getProductLinkField();
            /* @var $collection Collection */
            $collection = $this->productCollectionFactory->create();
            $collection->addIdFilter($productIds);
            foreach ($collection as $product) {
                /** * @var Product $product */
                $linkIds[] = $product->getData($linkField);
            }
        }
        return $linkIds;
    }

    /**
     * Retrieve just added to cart product object
     *
     * @return ProductInterface|null
     */
    private function getLastAddedProduct(): ?ProductInterface
    {
        $product = null;
        if ($this->_lastAddedProductId) {
            try {
                $product = $this->productRepository->getById($this->_lastAddedProductId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }
        }
        return $product;
    }

    /**
     * Retrieve Array of Product instances in Cart
     *
     * @return array
     */
    private function getCartProducts(): array
    {
        if ($this->cartProducts === null) {
            $this->cartProducts = [];
            foreach ($this->getQuote()->getAllItems() as $quoteItem) {
                /* @var $quoteItem Item */
                $product = $quoteItem->getProduct();
                if ($product) {
                    $this->cartProducts[$product->getEntityId()] = $product;
                    $this->_lastAddedProductId = $product->getEntityId();
                }
            }
        }
        return $this->cartProducts;
    }

    /**
     * Add all attributes and apply pricing logic to products collection
     * to get correct values in different products lists.
     * E.g. crosssells, upsells, new products, recently viewed
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function _addProductAttributesAndPrices(
        Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }
}
