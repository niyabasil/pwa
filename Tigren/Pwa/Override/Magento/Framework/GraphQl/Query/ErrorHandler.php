<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */
declare(strict_types=1);

namespace Tigren\Pwa\Override\Magento\Framework\GraphQl\Query;

use Magento\Framework\App\State;
use Magento\Framework\Exception\AggregateExceptionInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlAlreadyExistsException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Remove unnecessary graphql logs
 */
class ErrorHandler extends \Magento\Framework\GraphQl\Query\ErrorHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var State
     */
    private $appState;

    /**
     * @param LoggerInterface $logger
     * @param State $appState
     */
    public function __construct(LoggerInterface $logger, State $appState)
    {
        parent::__construct($logger, $appState);
        $this->logger = $logger;
        $this->appState = $appState;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $errors, callable $formatter): array
    {
        $formattedErrors = [];

        // When not in developer mode, only log & report the first error for performance implications
        if ($this->appState->getMode() !== State::MODE_DEVELOPER) {
            $errors = array_splice($errors, 0, 1);
        }

        foreach ($errors as $error) {
            $previousError = $error->getPrevious();

            if (
                !$previousError instanceof GraphQlAlreadyExistsException
                && !$previousError instanceof GraphQlAuthorizationException
                && !$previousError instanceof GraphQlInputException
                && !$previousError instanceof GraphQlNoSuchEntityException
                && !$previousError instanceof AuthenticationException
            ) {
                $this->logger->error($previousError);
            }

            if ($previousError instanceof AggregateExceptionInterface && !empty($previousError->getErrors())) {
                $aggregatedErrors = $previousError->getErrors();
                foreach ($aggregatedErrors as $aggregatedError) {
                    if (
                        !$previousError instanceof GraphQlAlreadyExistsException
                        && !$previousError instanceof GraphQlAuthorizationException
                        && !$previousError instanceof GraphQlInputException
                        && !$previousError instanceof GraphQlNoSuchEntityException
                        && !$previousError instanceof AuthenticationException
                    ) {
                        $this->logger->error($aggregatedError);
                    }
                    $formattedAggregatedError = $formatter($aggregatedError);
                    $formattedAggregatedError['message'] = __($formattedAggregatedError['message']);
                    $formattedErrors[] = $formattedAggregatedError;
                }
            } else {
                $formattedError = $formatter($error);
                $formattedError['message'] = __($formattedError['message']);
                $formattedErrors[] = $formattedError;
            }
        }

        return $formattedErrors;
    }
}
