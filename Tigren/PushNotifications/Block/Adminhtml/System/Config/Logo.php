<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Phrase;

/**
 * @method Logo setLogoText($data)
 * @method Logo setInputType($data)
 * @method Logo setValue($data)
 * @method Logo setImageUrl($data)
 * @method Logo setNamePrefix($data)
 * @method Logo setHtmlId($data)
 */
class Logo extends Field
{
    /**
     *
     */
    const CONFIG_PATH = 'tigren_notifications/design/logo';

    /**
     * @var string
     */
    protected $_template = 'Tigren_PushNotifications::system/config/logo.phtml';

    /**
     * @var string
     */
    private $value;

    /**
     * @return $this
     */
    private function prepareData(AbstractElement $element)
    {
        $value = $this->getValue();
        $url = '';
        $config = $element->getFieldConfig();

        if ($value && isset($config['base_url'])) {
            $configElement = $config['base_url'];
            $urlType = empty($configElement['type']) ? 'link' : (string)$configElement['type'];
            $url = $this->_urlBuilder->getBaseUrl(['_type' => $urlType]) . $configElement['value'] . '/' . $value;
        }

        $this->setLogoText($this->getEmptyLogoText())
            ->setInputType($element->getType())
            ->setValue($this->getValue())
            ->setImageUrl($url);

        return $this;
    }

    /**
     * Retrieve element HTML markup.
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->prepareData($element);
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());

        return $this->_toHtml() . $this->getDeleteCheckboxHtml($element) . $this->getHiddenInput($element);
    }

    /**
     * Return html code of hidden element
     *
     * @return string
     */
    private function getHiddenInput(AbstractElement $element)
    {
        return '<input type="hidden" name="' . $element->getName()
            . '[value]" value="' . $this->getValue() . '" />';
    }

    /**
     * @return string
     */
    private function getDeleteCheckboxHtml(AbstractElement $element)
    {
        return '<input type="hidden"' .
            ' name="' .
            $element->getName() .
            '[delete]" value="0" class="tgdelete-image"' .
            ' id="' .
            $element->getHtmlId() .
            '_delete"' .
            ($element->getDisabled() ? ' disabled="disabled"' : '') .
            '/>';
    }

    /**
     * @return int
     */
    private function getValue()
    {
        if ($this->value === null) {
            $data = $this->getConfigData();
            $this->value = isset($data[self::CONFIG_PATH]) ? $data[self::CONFIG_PATH] : '';
        }

        return $this->value;
    }

    /**
     * @return Phrase
     */
    public function getEmptyLogoText()
    {
        return __('Logo');
    }
}
