<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model\Resolver\Quote\Item;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssetImageFactory;
use Magento\Framework\View\ConfigInterface;

/**
 * Class ProductImage
 * @package Tigren\Pwa\Model\Resolver\Quote\Item
 */
class ProductImage implements ResolverInterface
{
    /**
     * @var PlaceholderFactory
     */
    private $viewAssetPlaceholderFactory;

    /**
     * @var AssetImageFactory
     */
    private $viewAssetImageFactory;

    /**
     * @var ConfigInterface
     */
    private $presentationConfig;

    /**
     * @var ParamsBuilder
     */
    private $imageParamsBuilder;

    /**
     * CartItemImage constructor.
     * @param PlaceholderFactory $viewAssetPlaceholderFactory
     * @param AssetImageFactory $viewAssetImageFactory
     * @param ConfigInterface $presentationConfig
     * @param ParamsBuilder $imageParamsBuilder
     */
    public function __construct(
        PlaceholderFactory $viewAssetPlaceholderFactory,
        AssetImageFactory $viewAssetImageFactory,
        ConfigInterface $presentationConfig,
        ParamsBuilder $imageParamsBuilder
    ) {
        $this->viewAssetPlaceholderFactory = $viewAssetPlaceholderFactory;
        $this->viewAssetImageFactory = $viewAssetImageFactory;
        $this->presentationConfig = $presentationConfig;
        $this->imageParamsBuilder = $imageParamsBuilder;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            return null;
        }

        /** @var Product $product */
        $product = $value['model']->getProduct();
        if (!$product || !$product->getId()) {
            return null;
        }

        $viewImageConfig = $this->presentationConfig->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            ImageHelper::MEDIA_TYPE_CONFIG_NODE,
            'cart_page_product_thumbnail'
        );

        $imageMiscParams = $this->imageParamsBuilder->build($viewImageConfig);
        $originalFilePath = $product->getData('small_image');

        if ($originalFilePath === null || $originalFilePath === 'no_selection') {
            $imageAsset = $this->viewAssetPlaceholderFactory->create(
                [
                    'type' => $imageMiscParams['image_type']
                ]
            );
        } else {
            $imageAsset = $this->viewAssetImageFactory->create(
                [
                    'miscParams' => $imageMiscParams,
                    'filePath' => $originalFilePath,
                ]
            );
        }

        return $imageAsset->getUrl();
    }
}
