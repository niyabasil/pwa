<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Customer;

use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class CreateCustomerAccount
 * @package Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Customer
 */
class CreateCustomerAccount
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
     * @param \Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount $subject
     * @param array $data
     * @param StoreInterface $store
     * @return array
     */
    public function beforeExecute(
        \Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount $subject,
        array $data,
        StoreInterface $store
    ) {
        $arr = [];
        if (isset($data['date_of_birth'])) {
            $arr['date_of_birth'] = $this->getDate($data['date_of_birth'],
                $this->locale->getLocale() == 'th_TH');

            $arr['dob'] = $arr['date_of_birth'];
        }
        $data = array_merge($data, $arr);

        return [$data, $store];
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
