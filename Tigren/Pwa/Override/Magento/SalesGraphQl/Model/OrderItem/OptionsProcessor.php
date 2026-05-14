<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2023 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Override\Magento\SalesGraphQl\Model\OrderItem;

use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class OptionsProcessor
 * @package Tigren\Pwa\Override\Magento\SalesGraphQl\Model\OrderItem
 */
class OptionsProcessor extends \Magento\SalesGraphQl\Model\OrderItem\OptionsProcessor
{
    /**
     * @param OrderItemInterface $orderItem
     * @return array[]
     */
    public function getItemOptions(OrderItemInterface $orderItem): array
    {
        //build options array
        $optionsTypes = ['selected_options' => [], 'entered_options' => []];
        $options = $orderItem->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $optionsTypes = $this->processOptions($options['options']);
            }
            if (isset($options['attributes_info'])) {
                $optionsTypes['selected_options'] = \array_merge(
                    $optionsTypes['selected_options'],
                    $this->processAttributesInfo($options['attributes_info'])
                );
            }
        }

        return $optionsTypes;
    }

    /**
     * @param array $options
     * @return array[]
     */
    private function processOptions(array $options): array
    {
        $selectedOptions = [];
        $enteredOptions = [];
        foreach ($options ?? [] as $option) {
            if (isset($option['option_type'])) {
                if (in_array($option['option_type'], ['field', 'area', 'file', 'date', 'date_time', 'time'])) {
                    $selectedOptions[] = [
                        'label' => $option['label'],
                        'value' => $option['print_value'] ?? $option['value'],
                    ];
                } elseif (in_array($option['option_type'], ['drop_down', 'radio', 'checkbox', 'multiple'])) {
                    $enteredOptions[] = [
                        'label' => $option['label'],
                        'value' => $option['print_value'] ?? $option['value'],
                    ];
                }
            }
        }
        return ['selected_options' => $selectedOptions, 'entered_options' => $enteredOptions];
    }

    /**
     * @param array $attributesInfo
     * @return array
     */
    private function processAttributesInfo(array $attributesInfo): array
    {
        $selectedOptions = [];
        foreach ($attributesInfo ?? [] as $option) {
            $selectedOptions[] = [
                'label' => $option['label'],
                'value' => $option['print_value'] ?? $option['value'],
            ];
        }
        return $selectedOptions;
    }
}
