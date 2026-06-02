<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Promo Banners Base for Magento 2
 */

namespace Amasty\PromoBanners\Controller\Adminhtml\Banners;

use Amasty\PromoBanners\Model\ResourceModel\Rule as ResourceRule;
use Amasty\PromoBanners\Model\Rule;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout\Builder;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * @var ResourceRule
     */
    private $resourceRule;

    public function __construct(
        Context $context,
        Builder $builder,
        ResourceRule $resourceRule,
        Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->builder = $builder;
        $this->resourceRule = $resourceRule;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');

        /** @var Rule $model */
        $model = $this->_objectManager->create(Rule::class);

        if ($id) {
            $this->resourceRule->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('Record does not exist.'));

                return $this->resultRedirectFactory->create()->setPath('ampromobanners/*');
            }
        }
        // set entered data if was error when we do save
        $data = $this->_session->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject(
            'banner_conditions_fieldset'
        );
        $model->getActions()->setJsFormObject(
            'ampromobanners_banner_productcond_fieldset'
        );

        $this->_coreRegistry->register('current_amasty_promo_banner', $model);
        $this->builder->build();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_PromoBanners::ampromobanners')
            ->addBreadcrumb(__('Banners'), __('Banners'));
        if ($model->getId()) {
            $title = __('Edit Banner `%1`', $model->getRuleName());
        } else {
            $title = __("Add new Banner");
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);

        $resultPage->renderResult($this->_response);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_PromoBanners::ampromobanners');
    }
}
