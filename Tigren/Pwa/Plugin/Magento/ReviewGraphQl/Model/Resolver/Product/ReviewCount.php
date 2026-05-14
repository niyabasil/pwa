<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Plugin\Magento\ReviewGraphQl\Model\Resolver\Product;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Review\Model\Review;
use Magento\Review\Model\Review\Config as ReviewsConfig;

/**
 * Class ReviewCount
 * @package Tigren\Pwa\Plugin\Magento\ReviewGraphQl\Model\Resolver\Product
 */
class ReviewCount
{
    /**
     * @var Review
     */
    private $review;

    /**
     * @var ReviewsConfig
     */
    private $reviewsConfig;

    /**
     * @param Review $review
     * @param ReviewsConfig $reviewsConfig
     */
    public function __construct(Review $review, ReviewsConfig $reviewsConfig)
    {
        $this->review = $review;
        $this->reviewsConfig = $reviewsConfig;
    }

    /**
     * @param \Magento\ReviewGraphQl\Model\Resolver\Product\ReviewCount $subject
     * @param $results
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return int
     */
    public function aroundResolve(
        \Magento\ReviewGraphQl\Model\Resolver\Product\ReviewCount $subject,
        \Closure $proceed,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (false === $this->reviewsConfig->isEnabled()) {
            return 0;
        }

        if (!isset($value['model'])) {
            throw new GraphQlInputException(__('Value must contain "model" property.'));
        }

        $product = $value['model'];

        return (int)$this->review->getTotalReviews($product->getId(), true,
            $context->getExtensionAttributes()->getStore()->getId());
    }
}
