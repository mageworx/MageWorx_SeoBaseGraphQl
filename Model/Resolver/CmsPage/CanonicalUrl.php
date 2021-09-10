<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoBaseGraphQl\Model\Resolver\CmsPage;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\Exception\LocalizedException;

class CanonicalUrl implements ResolverInterface
{
    /**
     * @var \MageWorx\SeoBase\Model\CanonicalFactory
     */
    protected $canonicalFactory;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $fullActionName = 'cms_page_view';

    /**
     * @param \MageWorx\SeoBase\Model\CanonicalFactory $canonicalFactory
     * @param PageRepositoryInterface $pageRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorx\SeoBase\Model\CanonicalFactory $canonicalFactory,
        PageRepositoryInterface $pageRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->canonicalFactory = $canonicalFactory;
        $this->pageRepository   = $pageRepository;
        $this->storeManager     = $storeManager;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value[PageInterface::PAGE_ID])) {
            throw new LocalizedException(__('"%1" value should be specified', PageInterface::PAGE_ID));
        }

        /** @var \Magento\Cms\Model\Page $cmsPage */
        $cmsPage   = $this->pageRepository->getById($value[PageInterface::PAGE_ID]);
        $arguments = ['fullActionName' => $this->fullActionName];

        /** @var \MageWorx\SeoBase\Model\CanonicalInterface $canonicalModel */
        $canonicalModel = $this->canonicalFactory->create($this->fullActionName, $arguments);
        $canonicalModel->setEntity($cmsPage);

        $canonicalUrl     = $canonicalModel->getCanonicalUrl();
        $canonicalStoreId = $canonicalModel->getCanonicalStoreId($cmsPage->getId());

        $result = [
            'url'  => $canonicalUrl ?: null,
            'code' => $canonicalStoreId ? $this->storeManager->getStore($canonicalStoreId)->getCode() : null
        ];

        return $result;
    }
}
