<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\PushNotifications\Model\Queue;

use Tigren\PushNotifications\Exception\NotificationException;
use Tigren\PushNotifications\Model\Processor\NotificationProcessor;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Consumer
 * @package Tigren\PushNotifications\Model\Queue
 */
class Consumer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NotificationProcessor
     */
    private $notificationProcessor;

    /**
     * Consumer constructor.
     *
     * @param SerializerInterface $serializer
     * @param NotificationProcessor $notificationProcessor
     * @param LoggerInterface $logger
     */
    public function __construct(
        SerializerInterface $serializer,
        NotificationProcessor $notificationProcessor,
        LoggerInterface $logger
    ) {
        $this->serializer = $serializer;
        $this->notificationProcessor = $notificationProcessor;
        $this->logger = $logger;
    }

    /**
     * Process
     *
     * @param string $data
     * @return void
     * @throws NotificationException
     */
    public function process(string $data)
    {
        $params = $this->serializer->unserialize($data);
        try {
            $this->notificationProcessor->process($params);
        } catch (AlreadyExistsException|CouldNotSaveException|NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
