<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Model\Campaign;
use Tigren\PushNotifications\Model\OptionSource\Campaign\Status;
use DateTime;
use Magento\Backend\App\Action\Context;
use Tigren\PushNotifications\Api\CampaignRepositoryInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class InlineEdit
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign
 */
class InlineEdit extends \Tigren\PushNotifications\Controller\Adminhtml\Campaign
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $repository;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    public function __construct(
        Context $context,
        CampaignRepositoryInterface $repository,
        TimezoneInterface $timezone
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->timezone = $timezone;
    }

    /**
     * InlineEdit Action
     * @return Json
     * @throws \Exception
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $postItems = $this->getRequest()->getParam('items', []);
        $errors = [];

        if ($postItems) {
            try {
                foreach ($postItems as $campaingId => $data) {
                    /** @var Campaign $model */
                    $model = $this->repository->getById($campaingId);
                    $data = $this->prepareData($data);
                    $model->addData($data);
                    $this->repository->save($model);
                }
            } catch (LocalizedException $e) {
                $errors = array_merge($errors, [$e->getMessage()]);
            }
        }

        return $resultJson->setData(
            [
                'messages' => $errors,
                'error' => !empty($errors)
            ]
        );
    }

    /**
     * @param array $data
     * @return array $data
     * @throws \Exception
     */
    private function prepareData($data)
    {
        if (isset($data[CampaignInterface::SCHEDULED])) {
            $data[CampaignInterface::SCHEDULED] = $this->timezone
                ->date(new DateTime($data[CampaignInterface::SCHEDULED]))
                ->format('Y-m-d H:i:s');
        }

        if (isset($data[CampaignInterface::CAMPAIGN_ID])) {
            unset($data[CampaignInterface::CAMPAIGN_ID]);
        }

        return $data;
    }
}
