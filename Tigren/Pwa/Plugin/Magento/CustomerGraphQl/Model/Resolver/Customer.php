<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver;

use Magento\Framework\Locale\Resolver;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Customer
 * @package Tigren\Pwa\Plugin\Magento\CustomerGraphQl\Model\Resolver
 */
class Customer
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
     * @param \Magento\CustomerGraphQl\Model\Resolver\Customer $subject
     * @param $result
     * @return mixed
     */
    public function afterResolve(
        \Magento\CustomerGraphQl\Model\Resolver\Customer $subject,
        $result
    ) {
        if (isset($result['date_of_birth'])) {
            $result['date_of_birth'] = $this->getDate($result['date_of_birth'],
                $this->locale->getLocale() == 'th_TH');

            $result['dob'] = $result['date_of_birth'];
        }
        return $result;
    }

    /**
     * @param string|null $dateTime
     * @param bool $isTH
     * @return string
     */
    public function getDate(string $dateTime = null, bool $isTH = false): string
    {
        $time = $this->dateTime->gmtDate('Y-m-d', strtotime($dateTime));
        if ($isTH) {
            return $this->dateTime->gmtDate('Y-m-d', strtotime($dateTime . "+" . 543 . " year"));
        }
        return $time;
    }
}
