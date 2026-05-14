<?php
/*
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Pwa\Plugin\Magento\CmsGraphQl\Model\Resolver\DataProvider;

use Closure;
use Exception;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Widget\Model\Template\FilterEmulate;

/**
 * Cms block data provider
 */
class Block
{
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;

    /**
     * @var FilterEmulate
     */
    private $widgetFilter;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param BlockRepositoryInterface $blockRepository
     * @param FilterEmulate $widgetFilter
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        BlockRepositoryInterface $blockRepository,
        FilterEmulate $widgetFilter,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->blockRepository = $blockRepository;
        $this->widgetFilter = $widgetFilter;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param \Magento\CmsGraphQl\Model\Resolver\DataProvider\Block $subject
     * @param Closure $proceed
     * @param string $blockIdentifier
     * @param int $storeId
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundGetBlockByIdentifier(
        \Magento\CmsGraphQl\Model\Resolver\DataProvider\Block $subject,
        Closure $proceed,
        string $blockIdentifier,
        int $storeId
    ) {
        $blockData = $this->fetchBlockData($blockIdentifier, BlockInterface::IDENTIFIER, $storeId);

        if (isset($blockData[BlockInterface::CONTENT])) {
            $blockData[BlockInterface::CONTENT] = str_replace(
                '<style>',
                htmlspecialchars('<style>'),
                $blockData[BlockInterface::CONTENT]
            );
            $blockData[BlockInterface::CONTENT] = str_replace(
                '</style>',
                htmlspecialchars('</style>'),
                $blockData[BlockInterface::CONTENT]
            );
        }

        return $blockData;
    }

    /**
     * Fetch black data by either id or identifier field
     *
     * @param mixed $identifier
     * @param string $field
     * @param int $storeId
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    private function fetchBlockData($identifier, string $field, int $storeId): array
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter($field, $identifier)
            ->addFilter(Store::STORE_ID, [$storeId, Store::DEFAULT_STORE_ID], 'in')
            ->addFilter(BlockInterface::IS_ACTIVE, true)->create();

        $blockResults = $this->blockRepository->getList($searchCriteria)->getItems();

        if (empty($blockResults)) {
            throw new NoSuchEntityException(
                __('The CMS block with the "%1" ID doesn\'t exist.', $identifier)
            );
        }

        $block = end($blockResults);
        $renderedContent = $this->widgetFilter->filterDirective($block->getContent());
        return [
            BlockInterface::BLOCK_ID => $block->getId(),
            BlockInterface::IDENTIFIER => $block->getIdentifier(),
            BlockInterface::TITLE => $block->getTitle(),
            BlockInterface::CONTENT => $renderedContent,
        ];
    }

    /**
     * @param \Magento\CmsGraphQl\Model\Resolver\DataProvider\Block $subject
     * @param Closure $proceed
     * @param int $blockId
     * @param int $storeId
     * @return mixed
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function aroundGetBlockById(
        \Magento\CmsGraphQl\Model\Resolver\DataProvider\Block $subject,
        Closure $proceed,
        int $blockId,
        int $storeId
    ) {
        $blockData = $this->fetchBlockData($blockId, BlockInterface::BLOCK_ID, $storeId);

        if (isset($blockData[BlockInterface::CONTENT])) {
            $blockData[BlockInterface::CONTENT] = str_replace(
                '<style>',
                htmlspecialchars('<style>'),
                $blockData[BlockInterface::CONTENT]
            );
            $blockData[BlockInterface::CONTENT] = str_replace(
                '</style>',
                htmlspecialchars('</style>'),
                $blockData[BlockInterface::CONTENT]
            );
        }

        return $blockData;
    }
}
