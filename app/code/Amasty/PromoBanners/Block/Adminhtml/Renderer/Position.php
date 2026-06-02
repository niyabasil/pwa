<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Block\Adminhtml\Renderer;

class Position extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options
{
    protected $_positionSource;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Amasty\PromoBanners\Model\Source\Position $positionSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_positionSource = $positionSource;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     *
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $position = $row->getData('banner_position');
        //$position = trim($position, ',');
        if (is_null($position)) {
            return __('No Position');
        }
        $position = explode(',', $position);

        $html = '';

        foreach ($this->_positionSource->toOptionArray() as $posId => $row) {
            if (in_array($posId, $position)) {
                $html .= $row . "<br />";
            }
        }
        return $html;
    }
}
