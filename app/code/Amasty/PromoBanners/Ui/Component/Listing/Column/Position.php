<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\PromoBanners\Model\Source\Position as SourcePosition;

class Position implements OptionSourceInterface
{
    private $options;

    /**
     * @var SourcePosition
     */
    private $position;

    public function __construct(SourcePosition $position)
    {
        $this->position = $position;
    }

    /**
     * @return array|null
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            foreach ($this->position->getPositionMulti() as $key => $option) {
                $this->options[] = [
                    'value' => $option['value'],
                    'label' => $option['label']
                ];
            }

            $this->options[] = ['value' => 'null', 'label' =>  __('No Position')];
        }

        return $this->options;
    }
}
