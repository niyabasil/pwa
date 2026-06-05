<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Search Base for Magento 2
 */

namespace Amasty\Xsearch\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class UserSearch extends AbstractDb
{
    public const MAIN_TABLE = 'amasty_xsearch_users_search';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }

    /**
     * @param int $days
     * @return void
     * @throws LocalizedException
     */
    public function deleteUserSearchOlderThan(int $days): void
    {
        $this->getConnection()
            ->delete(
                $this->getMainTable(),
                ['created_at < DATE_SUB(CURDATE(), INTERVAL ? DAY)' => $days]
            );
    }
}
