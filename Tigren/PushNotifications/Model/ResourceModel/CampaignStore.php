<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Model\ResourceModel;

use Exception;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Tigren\PushNotifications\Setup\Operation\CreateCampaignStoreTable;
use Tigren\PushNotifications\Api\Data\CampaignStoreInterface;

/**
 * Class CampaignStore
 * @package Tigren\PushNotifications\Model\ResourceModel
 */
class CampaignStore extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CreateCampaignStoreTable::TABLE_NAME, CampaignStoreInterface::ID);
    }

    /**
     * Store constructor.
     *
     * @param Context $context
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        return $this->entityManager->load($object, $value);
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this|AbstractDb
     *
     * @throws Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this|AbstractDb
     *
     * @throws Exception
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
