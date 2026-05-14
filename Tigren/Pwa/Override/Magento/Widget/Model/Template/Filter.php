<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Override\Magento\Widget\Model\Template;

use Magento\Catalog\Block\Widget\Link;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Filter
 * @package Tigren\Pwa\Override\Magento\Widget\Model\Template
 */
class Filter extends \Magento\Widget\Model\Template\Filter
{
    /**
     * General method for generate widget
     *
     * @param string[] $construction
     * @return string
     */
    public function generateWidget($construction)
    {
        $params = $this->getParameters($construction[2]);

        // Determine what name block should have in layout
        $name = null;
        if (isset($params['name'])) {
            $name = $params['name'];
        }

        if (isset($this->_storeId) && !isset($params['store_id'])) {
            $params['store_id'] = $this->_storeId;
        }

        // validate required parameter type or id
        if (!empty($params['type'])) {
            $type = $params['type'];
        } elseif (!empty($params['id'])) {
            $preConfigured = $this->_widgetResource->loadPreconfiguredWidget($params['id']);
            $type = $preConfigured['widget_type'];
            $params = $preConfigured['parameters'];
        } else {
            return '';
        }

        // we have no other way to avoid fatal errors for type like 'cms/widget__link', '_cms/widget_link' etc.
        $xml = $this->_widget->getWidgetByClassType($type);
        if ($xml === null) {
            return '';
        }

        // define widget block and check the type is instance of Widget Interface
        $widget = $this->_layout->createBlock($type, $name, ['data' => $params]);
        if (!$widget instanceof BlockInterface) {
            return '';
        }

        $html = $widget->toHtml();

        if ($widget instanceof Link) {
            $html = htmlspecialchars($html);
        }

        return $html;
    }
}
