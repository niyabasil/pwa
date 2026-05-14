<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Controller\Adminhtml\Campaign\FileUploader;

use Tigren\PushNotifications\Api\Data\CampaignInterface;
use Tigren\PushNotifications\Controller\Adminhtml\Campaign;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Tigren\PushNotifications\Model\FileUploader\FileProcessor;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Tigren\PushNotifications\Controller\Adminhtml\Campaign\FileUploader
 */
class Save extends Campaign
{
    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @param Context $context
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        Context $context,
        FileProcessor $fileProcessor
    ) {
        parent::__construct($context);
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $result = $this->fileProcessor->saveToTmp(CampaignInterface::LOGO_PATH);
        } catch (LocalizedException $exception) {
            $result = ['error' => $exception->getMessage()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
