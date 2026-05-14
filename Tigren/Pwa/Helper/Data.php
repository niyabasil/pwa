<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Helper;

use Magento\CatalogRule\Model\ResourceModel\Product\CollectionProcessor;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Helper for PWA extension
 */
class Data extends AbstractHelper
{
    /**
     * Configuration reader
     *
     * @var ReaderInterface
     */
    protected $reader;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheId;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var CollectionProcessor
     */
    private $catalogRuleProcessor;

    /**
     * @var array
     */
    private $graphQlConfig = [];

    /**
     * @var array
     */
    private $selectionCollection = [];

    /**
     * @param Context $context
     * @param CacheInterface $cache
     * @param ReaderInterface $reader
     * @param $cacheId
     * @param SerializerInterface|null $serializer
     * @param CollectionProcessor|null $catalogRuleProcessor
     */
    public function __construct(
        Context $context,
        CacheInterface $cache,
        ReaderInterface $reader,
        $cacheId,
        SerializerInterface $serializer = null,
        ?CollectionProcessor $catalogRuleProcessor = null
    ) {
        parent::__construct($context);
        $this->cache = $cache;
        $this->cacheId = $cacheId;
        $this->reader = $reader;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(SerializerInterface::class);
        $this->catalogRuleProcessor = $catalogRuleProcessor ?? ObjectManager::getInstance()
            ->get(CollectionProcessor::class);
    }

    /**
     * Prepare graphql configuration
     */
    public function prepareGraphqlConfig()
    {
        $this->graphQlConfig = $this->reader->read();
    }

    /**
     * Warm up graphql configuration cache
     */
    public function warmUpGraphqlConfigCache()
    {
        $data = $this->cache->load($this->cacheId);
        if (false === $data && is_array($this->graphQlConfig) && !empty($this->graphQlConfig)) {
            $this->cache->save($this->serializer->serialize($this->graphQlConfig), $this->cacheId);
        }
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getProductSelectionItems($product)
    {
        if (!isset($this->selectionCollection[$product->getId()])) {
            $typeInstance = $product->getTypeInstance();
            $typeInstance->setStoreFilter($product->getStoreId(), $product);

            $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product),
                $product
            );
            $this->catalogRuleProcessor->addPriceData($selectionCollection);
            $selectionCollection->addTierPriceData();

            $this->selectionCollection[$product->getId()] = $selectionCollection->getItems();
        }

        return $this->selectionCollection[$product->getId()];
    }
}
