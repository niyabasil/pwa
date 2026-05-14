<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model;

use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class MagentoVersion is used for faster retrieving magento version
 */
class MagentoVersion
{
    const MAGENTO_VERSION = 'tigren_magento_version';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var Config
     */
    private $cache;

    /**
     * @var string
     */
    private $magentoVersion;

    /**
     * @param Config $cache
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Config $cache,
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
        $this->cache = $cache;
    }

    /**
     * @return string
     */
    public function get()
    {
        if (!$this->magentoVersion
            && !($this->magentoVersion = $this->cache->load(self::MAGENTO_VERSION))
        ) {
            $this->magentoVersion = $this->productMetadata->getVersion();
            $this->cache->save($this->magentoVersion, self::MAGENTO_VERSION);
        }

        return $this->magentoVersion;
    }
}
