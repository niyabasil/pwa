<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\Catalog\Model;

use Magento\Catalog\Api\Data\CategoryAttributeInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Class Category
 * @package Tigren\Pwa\Plugin\Magento\Catalog\Model
 */
class Category
{
    const THUMBNAIL = 'thumbnail';

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        LoggerInterface $logger
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Catalog\Model\Category $subject
     * @param $result
     * @return mixed
     */
    public function afterGetAttributes(\Magento\Catalog\Model\Category $subject, $result)
    {
        if (!isset($result[self::THUMBNAIL])) {
            try {
                $attribute = $this->attributeRepository->get(
                    CategoryAttributeInterface::ENTITY_TYPE_CODE,
                    self::THUMBNAIL
                );
                $result[self::THUMBNAIL] = $attribute;
            } catch (LocalizedException $e) {
                $this->logger->critical($e);
            }
        }

        return $result;
    }
}
