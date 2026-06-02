<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\ViewModel;

use Amasty\PromoBanners\Model\Banner\Data;
use Amasty\PromoBanners\Model\Rule;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\Registry;

class BannersDataProvider implements ArgumentInterface
{
    public const SEARCH_PAGE_URL = '/catalogsearch/result/';

    /**
     * @var Data
     */
    private $dataSource;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * Needs to use Registry, because on different pages, request ID param applies to Product or Category
     * @var Registry
     */
    private $registry;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        Data $dataSource,
        Escaper $escaper,
        Registry $registry,
        RequestInterface $request,
        Json $json,
        ProductRepositoryInterface $productRepository = null
    ) {
        $this->dataSource = $dataSource;
        $this->escaper = $escaper;
        $this->registry = $registry;
        $this->request = $request;
        $this->json = $json;
        $this->productRepository = $productRepository
            ?? ObjectManager::getInstance()->get(ProductRepositoryInterface::class);
    }

    public function getBanners(): array
    {
        $product = $categoryId = $searchQuery = null;

        /** @var \Magento\Catalog\Model\Product $product */
        if ($this->registry->registry('current_product')) {
            $product = $this->registry->registry('current_product');
        } else {
            $product = $this->getProduct((int)$this->request->getParam('productId'));
        }

        if ($this->registry->registry('current_category')) {
            $category = $this->registry->registry('current_category');
            $categoryId = (int)$category->getId();
        } else {
            $categoryId = (int)$this->request->getParam('categoryId');
        }

        if (str_contains($this->request->getPathInfo(), self::SEARCH_PAGE_URL)) {
            $searchQuery = $this->escaper->escapeUrl($this->request->getParam(QueryFactory::QUERY_VAR_NAME));
        }

        $bannersDetails = $this->dataSource->getBanners($product, $categoryId, $searchQuery);
        $bannersDetails['injectorSectionId'] = Rule::POS_AMONG_PRODUCTS;

        return $bannersDetails;
    }

    /**
     * @param array|bool|float|int|null|string $data
     * @return bool|string
     */
    public function serialize($data)
    {
        return $this->json->serialize($data);
    }

    private function getProduct(int $productId): ?ProductInterface
    {
        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            $product = null;
        }

        return $product;
    }
}
