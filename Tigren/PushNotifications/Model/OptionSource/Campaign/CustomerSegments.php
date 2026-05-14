<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\OptionSource\Campaign;

use Tigren\Segments\Model\ResourceModel\Segment\Collection;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class CustomerSegments
 */
class CustomerSegments implements OptionSourceInterface
{
    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        if ($this->moduleManager->isOutputEnabled('Tigren_Segments')) {
            $segmentCollection = $this->objectManager
                ->create(Collection::class)
                ->addActiveFilter();

            foreach ($segmentCollection->getItems() as $item) {
                $result[] = [
                    'value' => $item->getSegmentId(),
                    'label' => $item->getName()
                ];
            }
        }

        return $result;
    }
}
