<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\Campaign;

use Tigren\PushNotifications\Model\ConfigProvider;
use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;

/**
 * Class ApiKeyNotification
 * @package Tigren\PushNotifications\Block\Adminhtml\Campaign
 */
class ApiKeyNotification extends Container
{
    /**
     * Url path
     */
    const URL_PATH_CONFIG_SECTION = 'adminhtml/system_config/edit';

    /**
     * @var string
     */
    protected $_template = 'grid/api_key_notification.phtml';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->configProvider->getFirebaseApiKey()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getNotificationMessage()
    {
        $routePath = $this->getRequest()->getActionName();

        return __('Firebase API key is not set.') .
            ($routePath == 'edit' ? __(' Test notification won\'t be sent.') : '')
            . __(
                ' Please click <a href="%1">here</a> to submit an API key to your configuration.',
                $this->getSectionLink()
            );
    }

    /**
     * @return string
     */
    private function getSectionLink()
    {
        return $this->getUrl(self::URL_PATH_CONFIG_SECTION, ['section' => $this->configProvider->getPathPrefix()]);
    }
}
