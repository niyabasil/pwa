<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Override\Magento\Translation\Model\ResourceModel;

use Exception;
use Magento\Framework\App\Config;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Translation\App\Config\Type\Translation;

/**
 * Cms block data provider
 */
class Translate extends \Magento\Translation\Model\ResourceModel\Translate
{
    /**
     * @var Config
     */
    private $appConfig;

    /**
     * @param Context $context
     * @param ScopeResolverInterface $scopeResolver
     * @param string $connectionName
     * @param null|string $scope
     * @param Config|null $appConfig
     * @param DeploymentConfig|null $deployedConfig
     */
    public function __construct(
        Context $context,
        ScopeResolverInterface $scopeResolver,
        $connectionName = null,
        $scope = null,
        ?Config $appConfig = null,
        ?DeploymentConfig $deployedConfig = null
    ) {
        parent::__construct($context, $scopeResolver, $connectionName, $scope, $appConfig, $deployedConfig);
        $this->appConfig = $appConfig ?? ObjectManager::getInstance()->get(Config::class);
    }

    /**
     * Retrieve translation array for store / locale code
     *
     * @param int|null $storeId
     * @param string|null $locale
     *
     * @return array
     * @throws LocalizedException
     */
    public function getTranslationArray($storeId = null, $locale = null)
    {
        if ($storeId === null) {
            $storeId = $this->getStoreId();
        }
        $locale = (string)$locale;

        try {
            $data = $this->appConfig->get(
                Translation::CONFIG_TYPE,
                $locale . '/' . $this->getStoreCode($storeId),
                []
            );
        } catch (Exception $e) {
            $data = [];
        }

        $connection = $this->getConnection();
        if ($connection) {
            $select = $connection->select()
                ->from($this->getMainTable(), ['string', 'translate'])
                ->where('store_id IN (0 , :store_id)')
                ->where('locale = :locale')
                ->order('store_id');
            $bind = [':locale' => $locale, ':store_id' => $storeId];
            $dbData = array_map(function ($value) {
                if (!$value) {
                    return '';
                }
                return htmlspecialchars_decode($value);
            }, $connection->fetchPairs($select, $bind));
            $data = array_replace($data, $dbData);
        }

        return $data;
    }

    /**
     * Retrieve store code by store id
     *
     * @param int $storeId
     *
     * @return string
     */
    private function getStoreCode($storeId)
    {
        return $this->scopeResolver->getScope($storeId)->getCode();
    }
}
