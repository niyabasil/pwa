<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\UpwardConnector\Controller;

use Closure;
use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Upward
 * @package Tigren\Pwa\Plugin\Magento\UpwardConnector\Controller
 */
class Upward
{
    const PWA_CUSTOM_SCRIPT_XML_PATH = 'pwa/general/custom_script';
    const RECAPTCHA_FRONTEND_PUBLIC_KEY_XML_PATH = 'recaptcha_frontend/type_recaptcha_v3/public_key';
    const RECAPTCHA_FRONTEND_POSITION_XML_PATH = 'recaptcha_frontend/type_recaptcha_v3/position';
    const RECAPTCHA_FRONTEND_THEME_XML_PATH = 'recaptcha_frontend/type_recaptcha_v3/theme';
    const RECAPTCHA_FRONTEND_LANG_XML_PATH = 'recaptcha_frontend/type_recaptcha_v3/lang';
    const RECAPTCHA_FRONTEND_TYPE_FOR_CUSTOMER_LOGIN_XML_PATH = 'recaptcha_frontend/type_for/customer_login';
    const RECAPTCHA_FRONTEND_TYPE_FOR_CUSTOMER_CREATE_XML_PATH = 'recaptcha_frontend/type_for/customer_create';
    const RECAPTCHA_FRONTEND_TYPE_FOR_CUSTOMER_FORGOT_XML_PATH = 'recaptcha_frontend/type_for/customer_forgot_password';
    const RECAPTCHA_V3 = 'recaptcha_v3';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Upward constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\UpwardConnector\Controller\Upward $subject
     * @param Closure $proceed
     * @param RequestInterface $request
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function aroundDispatch(
        \Magento\UpwardConnector\Controller\Upward $subject,
        Closure $proceed,
        RequestInterface $request
    ) {
        $result = $proceed($request);

        $content = $result->getContent();

        $store = $this->storeManager->getStore();

        $customScripts = $this->getDataConfig($store, self::PWA_CUSTOM_SCRIPT_XML_PATH) ?: '';

        $recaptchaConfig = [
            'public_key' => $this->getDataConfig($store, self::RECAPTCHA_FRONTEND_PUBLIC_KEY_XML_PATH) ?: '',
            'position' => $this->getDataConfig($store, self::RECAPTCHA_FRONTEND_POSITION_XML_PATH) ?: '',
            'theme' => $this->getDataConfig($store, self::RECAPTCHA_FRONTEND_THEME_XML_PATH) ?: '',
            'lang' => $this->getDataConfig($store, self::RECAPTCHA_FRONTEND_LANG_XML_PATH) ?: '',
            'forms' => [
                'customer_login' => $this->getDataConfig($store,
                        self::RECAPTCHA_FRONTEND_TYPE_FOR_CUSTOMER_LOGIN_XML_PATH) == self::RECAPTCHA_V3,
                'customer_create' => $this->getDataConfig($store,
                        self::RECAPTCHA_FRONTEND_TYPE_FOR_CUSTOMER_CREATE_XML_PATH) == self::RECAPTCHA_V3,
                'customer_forgot_password' => $this->getDataConfig($store,
                        self::RECAPTCHA_FRONTEND_TYPE_FOR_CUSTOMER_FORGOT_XML_PATH) == self::RECAPTCHA_V3
            ]
        ];

        try {
            $recaptchaConfig = json_encode($recaptchaConfig);
        } catch (Exception $e) {
            $recaptchaConfig = '';
        }

        if ($recaptchaConfig) {
            $customScripts .= sprintf('<script type="text/javascript">window.recaptchaConfig=%s</script>',
                $recaptchaConfig);
        }

        $content = str_replace('<script id="custom_script"></script>', $customScripts, $content);
        $result->setContent($content);

        $contentType = $result->getHeader('Content-Type');
        if ($contentType && $contentType->getFieldValue() == 'text/html') {
            $result->setHeader('Cache-Control', 'public, max-age=' . 86400 . ', s-maxage=' . 86400, true);
        }

        return $result;
    }

    /**
     * @param $store
     * @param $requestPath
     * @return mixed
     */
    protected function getDataConfig($store, $requestPath)
    {
        return $this->scopeConfig->getValue(
            $requestPath,
            ScopeInterface::SCOPE_STORE,
            (int)$store->getId()
        );
    }
}
