<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class UpdateCustomer
 * @package Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver
 */
class UpdateCustomer
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Resolver
     */
    protected $locale;

    /**
     * @param DateTime $dateTime
     * @param Resolver $locale
     */
    public function __construct(
        DateTime $dateTime,
        Resolver $locale
    ) {
        $this->dateTime = $dateTime;
        $this->locale = $locale;
    }


    /**
     * @param \Magento\CustomerGraphQl\Model\Resolver\UpdateCustomer $subject
     * @param $result
     * @return mixed
     */
    public function beforeResolve(
        \Magento\CustomerGraphQl\Model\Resolver\UpdateCustomer $subject,
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (isset($args['input']['date_of_birth'])) {
            $args['input']['date_of_birth'] = $this->getDate($args['input']['date_of_birth'],
                $this->locale->getLocale() == 'th_TH');

            $args['input']['dob'] = $args['input']['date_of_birth'];
        }
        return [$field, $context, $info, $value, $args];
    }

    /**
     * @param string|null $dateTime
     * @param bool $isTH
     * @return string
     */
    public function getDate(string $dateTime = null, bool $isTH = false): string
    {
        $dateTimeArr = explode('/', $dateTime);
        if (count($dateTimeArr) == 3) {
            $year = $isTH ? ($dateTimeArr['2'] - 543) : $dateTimeArr['2'];
            $month = $dateTimeArr['1'];
            $day = $dateTimeArr['0'];
            $dateTime = date("Y-m-d", strtotime("{$year}-{$month}-{$day}"));
        }
        return $dateTime;
    }
}
