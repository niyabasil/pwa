<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation;

use Magento\Eav\Model\Config;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Swatches\Helper\Data;

/**
 * Class LayerBuilder
 * @package Tigren\Pwa\Plugin\Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation
 */
class LayerBuilder
{
    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var Data
     */
    private $swatchHelper;

    /**
     * @param Config $eavConfig
     * @param Data $swatchHelper
     */
    public function __construct(
        Config $eavConfig,
        Data $swatchHelper
    ) {
        $this->eavConfig = $eavConfig;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * @param \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\LayerBuilder $subject
     * @param $result
     * @param AggregationInterface $aggregation
     * @param int $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterBuild(
        \Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\LayerBuilder $subject,
        $result,
        AggregationInterface $aggregation,
        int $storeId
    ): array {
        $hashcodeData = $this->getSwatches($result);

        foreach ($result as &$value) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $value['attribute_code']);
            if ($this->swatchHelper->isSwatchAttribute($attribute)) {
                foreach ($value['options'] as &$data) {
                    $data['image'] = $hashcodeData[$data['value']]['value'];
                }
            }
        }
        return \array_filter($result);
    }

    /**
     * @param $data
     * @return array
     * @throws LocalizedException
     */
    public function getSwatches($data): array
    {
        $options = [];
        foreach ($data as &$value) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $value['attribute_code']);
            if ($this->swatchHelper->isSwatchAttribute($attribute)) {
                foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                    foreach (is_array($option['value']) ? $option['value'] : [$option] as $innerOption) {
                        if (strlen($innerOption['value'])) {
                            $options[$innerOption['value']] = (string)$innerOption['value'];
                        }
                    }
                }
            }
        }

        $swatchOptionsIds = array_keys($options);
        return $this->swatchHelper->getSwatchesByOptionsId($swatchOptionsIds);
    }
}
