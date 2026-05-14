<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\Campaign\Form;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template;

/**
 * Class LocalTime
 * @package Tigren\PushNotifications\Block\Adminhtml\Campaign\Form
 */
class LocalTime extends Template
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        Template\Context $context,
        ResourceConnection $resourceConnection,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->resourceConnection = $resourceConnection;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return string
     */
    private function getSystemTime()
    {
        $time = $this->_localeDate->date()->format('Y-m-d H:i:s');

        return $time;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getDefaultHtml($this->getSystemTime());
    }

    /**
     * Get the default html.
     *
     * @return string
     */
    public function getDefaultHtml($time)
    {
        $label = __("Local Time:");
        $html = '<div class="admin__field" style="padding-left: 19.5%">' . "\n";
        $html .= $this->getLabelHtml($label);
        $html .= $this->getElementHtml($time);
        $html .= '</div>' . "\n";

        return $html;
    }

    /**
     * @param string $label
     * @return string
     */
    public function getLabelHtml($label)
    {
        $html = '<label class="label admin__field-label" style="width: 10%;" for="' .
            $this->getHtmlId() . '"' . '><span>' . $label . '</span></label>' . "\n";

        return $html;
    }

    /**
     * @param string $time
     * @return string
     */
    public function getElementHtml($time)
    {
        $html = '';
        $htmlId = $this->getHtmlId();
        $html .= '<span id="' . $htmlId . '">' . $time . '</span>' . "\n";

        return $html;
    }
}
