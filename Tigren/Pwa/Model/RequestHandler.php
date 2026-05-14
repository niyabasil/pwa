<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Tigren\Pwa\Model;

use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaUi\Model\ErrorMessageConfigInterface;

/**
 * @inheritdoc
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RequestHandler
{
    /**
     * @var ValidationConfigResolverInterface
     */
    private $validationConfigResolver;

    /**
     * @var ValidatorInterface
     */
    private $captchaValidator;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ErrorMessageConfigInterface|null
     */
    private $errorMessageConfig;
    private $messageManager;
    private $actionFlag;
    private $validationErrorMessagesProvider;

    /**
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param ErrorMessageConfigInterface|null $errorMessageConfig
     */
    public function __construct(
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        RequestInterface $request,
        ?ErrorMessageConfigInterface $errorMessageConfig = null
    ) {
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->request = $request;
        $this->errorMessageConfig = $errorMessageConfig
            ?? ObjectManager::getInstance()->get(ErrorMessageConfigInterface::class);
        $this->validationErrorMessagesProvider = $validationErrorMessagesProvider
            ?? ObjectManager::getInstance()->get(ValidationErrorMessagesProvider::class);
    }

    /**
     * @inheritdoc
     * @param string $key
     * @throws GraphQlInputException
     * @throws InputException
     */
    public function execute(
        string $key
    ): void {
        $validationConfig = $this->validationConfigResolver->get($key);

        $reCaptchaResponse = $this->request->getHeader('X-recaptcha');
        $isRequiredRecaptcha = $this->request->getHeader('required-recaptcha');

        if (!$isRequiredRecaptcha) {
            return;
        }

        if (!$reCaptchaResponse) {
            throw new GraphQlInputException(__('Can not get recaptcha response.'));
        }

        $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
        if (false === $validationResult->isValid()) {
            $validationErrorText = $this->errorMessageConfig->getValidationFailureMessage();
            $technicalErrorText = $this->errorMessageConfig->getTechnicalFailureMessage();

            $errorMessages = $validationResult->getErrors();

            $message = $errorMessages ? $validationErrorText : $technicalErrorText;

            foreach ($errorMessages as $errorMessageCode => $errorMessageText) {
                if (!$this->isValidationError($errorMessageCode)) {
                    $message = $technicalErrorText;
                    throw new GraphQlInputException(__(
                        'reCAPTCHA \'%1\' form error: %2',
                        $key,
                        $errorMessageText
                    ));
                }
            }
        }
    }

    /**
     * Check if error code present in validation errors list.
     *
     * @param string $errorMessageCode
     * @return bool
     */
    private function isValidationError(string $errorMessageCode): bool
    {
        return $errorMessageCode !== $this->validationErrorMessagesProvider->getErrorMessage($errorMessageCode);
    }
}
