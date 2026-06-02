<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Model\Factories;

class SegmentFactory
{
    public const CUSTOMER_ID = 'customer_id';
    public const SEGMENT_ID = 'segment_id';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SegmentFactory constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Amasty\Segments\Model\ResourceModel\Segment\Collection
     */
    public function getSegmentCollection()
    {
        return $this->objectManager->create(\Amasty\Segments\Model\ResourceModel\Segment\Collection::class);
    }

    /**
     * @return \Amasty\Segments\Model\ResourceModel\Index
     */
    public function getSegmentIndex()
    {
        return $this->objectManager->create(\Amasty\Segments\Model\ResourceModel\Index::class);
    }
}
