<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\Framework\App;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class AreaList
 * @package Tigren\Pwa\Plugin\Magento\Framework\App
 */
class AreaList
{
    const XML_PATH_WEB_UPWARD_ENABLED = 'web/upward/enabled';
    const XML_PATH_WEB_UPWARD_PRERENDER = 'web/upward/prerender_enabled';
    const XML_PATH_WEB_UPWARD_PRERENDER_TOKEN = 'web/upward/prerender_token';
    const XML_PATH_WEB_UPWARD_PRERENDER_URL = 'web/upward/prerender_url';
    const XML_PATH_WEB_UPWARD_PRERENDER_CRAWLERS = 'web/upward/prerender_crawlers';
    const XML_PATH_WEB_UPWARD_PRERENDER_ALLOWED_LIST = 'web/upward/prerender_allowed_list';
    const XML_PATH_WEB_UPWARD_PRERENDER_BLOCKED_LIST = 'web/upward/prerender_blocked_list';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface $request
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    /**
     * Add pwa area code by front name
     *
     * @param \Magento\Framework\App\AreaList $subject
     * @param string|null $result
     * @param string $frontName
     *
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetCodeByFrontName(
        \Magento\Framework\App\AreaList $subject,
        $result,
        $frontName
    ) {
        $enabled = (bool)$this->scopeConfig->getValue(
            self::XML_PATH_WEB_UPWARD_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
        if ($result === 'pwa' && (!$enabled || $this->request->getServer('PWA_DISABLED'))) {
            return 'frontend';
        }

        $prerenderToken = $this->getPrerenderToken();
        $prerenderUrl = $this->getPrerenderUrl();
        if ($result === 'pwa' && (!$prerenderToken || !$prerenderUrl) && $this->shouldShowPrerenderedPage()) {
            return 'frontend';
        }

        return $result;
    }

    /**
     * Check if resources should be prerendered.
     *
     * @return bool
     */
    public function shouldShowPrerenderedPage()
    {
        if (!$this->getPrerenderUrl() ||
            !$this->scopeConfig->getValue(static::XML_PATH_WEB_UPWARD_PRERENDER, scopeInterface::SCOPE_STORE)
        ) {
            return false;
        }

        $request = $this->request;
        $requestUri = $request->getRequestUri();
        $referer = $request->getHeader('referer');

        if (!$request->isGet()) {
            return false;
        }

        if (!$this->isInAllowedList($requestUri)) {
            return false;
        }

        // we also check for a blocked referer
        $uris = array_filter([$requestUri, ($referer ? $referer : '')]);
        if ($this->isInBlockedList($uris)) {
            return false;
        }

        if (!$this->isCrawlerUserAgent($request)) {
            return false;
        }

        return true;
    }

    /**
     * Get prerender token from configuration.
     *
     * @return string|null
     */
    private function getPrerenderToken()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_WEB_UPWARD_PRERENDER_TOKEN);
    }

    /**
     * Get prerender url from configuration.
     *
     * @return string|null
     */
    private function getPrerenderUrl()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_WEB_UPWARD_PRERENDER_URL);
    }

    /**
     * Check if user agent is crawler bot.
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function isCrawlerUserAgent(RequestInterface $request)
    {
        $userAgent = strtolower($request->getServer('HTTP_USER_AGENT'));
        if (!$userAgent) {
            return false;
        }

        $bufferAgent = $request->getServer('X-BUFFERBOT');

        // prerender if _escaped_fragment_ is in the query string
        if ($bufferAgent || $request->getQuery('_escaped_fragment_')) {
            return true;
        }

        $crawlerUserAgents = $this->getList(
            (string)$this->scopeConfig->getValue(self::XML_PATH_WEB_UPWARD_PRERENDER_CRAWLERS)
        );

        foreach ($crawlerUserAgents as $crawlerUserAgent) {
            if (strpos(strtolower($userAgent), strtolower($crawlerUserAgent)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if uri is in allowed list.
     *
     * @param string $requestUri
     * @return bool
     */
    private function isInAllowedList(string $requestUri)
    {
        $allowedList = $this->getList(
            (string)$this->scopeConfig->getValue(self::XML_PATH_WEB_UPWARD_PRERENDER_ALLOWED_LIST)
        );

        return empty($allowedList) || $this->isListed([$requestUri], $allowedList);
    }

    /**
     * Checks if uri is in blocked list.
     *
     * @param array $uris
     * @return bool
     */
    private function isInBlockedList(array $uris)
    {
        $blockedList = $this->getList(
            (string)$this->scopeConfig->getValue(self::XML_PATH_WEB_UPWARD_PRERENDER_BLOCKED_LIST)
        );

        return !empty($blockedList) && $this->isListed($uris, $blockedList);
    }

    /**
     * Transforms string from configuration to the array.
     *
     * @param string $list
     * @return string[] array
     */
    private function getList(string $list)
    {
        return array_filter(
            array_map(
                'trim',
                preg_split("/(\r\n|\n)/", $list ?? '')
            )
        );
    }

    /**
     * Checks if provided uri is listed in the list from configuration.
     *
     * @param array $needles
     * @param array $list
     * @return bool
     */
    private function isListed(array $needles, array $list)
    {
        foreach ($list as $pattern) {
            foreach ($needles as $needle) {
                if (fnmatch($pattern, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }
}
