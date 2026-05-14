<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\OptionSource\Campaign;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection;
use Magento\Customer\Model\Group as CustomerGroup;

/**
 * Class CustomerGroups
 */
class CustomerGroups implements OptionSourceInterface
{
    /**
     * @var Collection
     */
    private $customerGroupCollection;

    public function __construct(
        Collection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->customerGroupCollection->setIgnoreIdFilter([CustomerGroup::NOT_LOGGED_IN_ID])->toOptionArray();
    }
}
