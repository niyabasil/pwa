<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Override\Magento\CustomerGraphQl\Model\Resolver;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class IsSubscribed
 * @package Tigren\Pwa\Override\Magento\CustomerGraphQl\Model\Resolver
 */
class IsSubscribed extends \Magento\CustomerGraphQl\Model\Resolver\IsSubscribed
{
    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param SubscriberFactory $subscriberFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->subscriberFactory = $subscriberFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var CustomerInterface $customer */
        $customer = $value['model'];
        $customerId = (int)$customer->getId();
        $websiteId = (int)$this->_storeManager->getWebsite()->getId();
        $status = $this->subscriberFactory->create()->loadByCustomer($customerId, $websiteId)->isSubscribed();

        return (bool)$status;
    }
}
