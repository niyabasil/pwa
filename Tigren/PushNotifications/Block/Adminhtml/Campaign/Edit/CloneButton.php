<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */


namespace Tigren\PushNotifications\Block\Adminhtml\Campaign\Edit;

use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class CloneButton
 * @package Tigren\PushNotifications\Block\Adminhtml\Campaign\Edit
 */
class CloneButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var FormKey
     */
    private $formKey;

    public function __construct(
        UrlInterface $urlBuilder,
        RequestInterface $request,
        FormKey $formKey
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->formKey = $formKey;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($id = $this->request->getParam('id')) {
            $alertMessage = __('New company will be created. Are you sure?');
            $onClick = sprintf('deleteConfirm("%s", "%s")', $alertMessage, $this->getCloneActionUrl($id));
            return [
                'label' => __('Clone Campaign'),
                'class' => 'save',
                'on_click' => sprintf("location.href = '%s';", $this->getCloneActionUrl($id)),
                'sort_order' => 60
            ];
        }

        return [];
    }

    /**
     * @param $id
     *
     * @return string
     */
    private function getCloneActionUrl($id)
    {
        return $this->urlBuilder->getUrl('*/*/cloneCampaign', ['id' => $id]);
    }
}
