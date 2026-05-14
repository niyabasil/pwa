<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Subscriber;

use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Tigren\PushNotifications\Controller\Adminhtml\AbstractMassAction;
use Tigren\PushNotifications\Model\CampaignFactory;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\Collection;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Tigren\PushNotifications\Model\ResourceModel\Subscriber\CollectionFactory as SubcriberCollectionFactory;
use Tigren\PushNotifications\Model\SubscriberRepository;
use Magento\Backend\App\Action;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class MassDelete
 * @package Tigren\PushNotifications\Controller\Adminhtml\Subscriber
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @var SubcriberCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SubscriberRepository
     */
    private $subscriberRepository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CampaignRepositoryInterface $repository,
        CollectionFactory $campaignCollectionFactory,
        CampaignFactory $campaignFactory,
        SubcriberCollectionFactory $collectionFactory,
        SubscriberRepository $subscriberRepository
    ) {
        parent::__construct($context, $filter, $logger, $repository, $campaignCollectionFactory, $campaignFactory);
        $this->collectionFactory = $collectionFactory;
        $this->subscriberRepository = $subscriberRepository;
    }

    /**
     * @return Collection
     */
    protected function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @param $item
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Tigren\PushNotifications\Exception\NotificationException
     */
    protected function itemAction($item)
    {
        $this->subscriberRepository->deleteById($item->getSubscriberId());
    }

    /**
     * @return Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t delete item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been deleted.', $collectionSize);
        }

        return __('No records have been deleted.');
    }
}
