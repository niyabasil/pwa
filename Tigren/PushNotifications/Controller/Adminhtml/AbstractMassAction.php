<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml;

use Tigren\PushNotifications\Model\ResourceModel\Campaign\Collection;
use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Tigren\PushNotifications\Model\ResourceModel\Campaign\CollectionFactory;
use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Tigren\PushNotifications\Model\CampaignFactory;
use Tigren\PushNotifications\Api\Data\CampaignInterface;

/**
 * Class AbstractMassAction
 * @package Tigren\PushNotifications\Controller\Adminhtml
 */
abstract class AbstractMassAction extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Tigren_PushNotifications::notifications_campaign';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    protected $campaignCollectionFactory;

    /**
     * @var CampaignRepositoryInterface
     */
    protected $repository;

    /**
     * @var CampaignFactory
     */
    protected $campaignFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        CampaignRepositoryInterface $repository,
        CollectionFactory $campaignCollectionFactory,
        CampaignFactory $campaignFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->campaignFactory = $campaignFactory;
    }

    /**
     * Execute action for item
     *
     * @param $item
     */
    abstract protected function itemAction($item);

    /**
     * @return Collection
     */
    protected function getCollection()
    {
        return $this->campaignCollectionFactory->create();
    }

    /**
     * Mass action execution
     * @throws LocalizedException
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0
        /** @var Collection $collection */
        $collection = $this->filter->getCollection($this->getCollection());

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $item) {
                    $this->itemAction($item);
                }

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @return Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }
}
