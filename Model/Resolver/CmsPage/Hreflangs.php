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
use MageWorx\SeoBase\Model\HreflangsFactory;
use MageWorx\SeoBase\Helper\Data as HelperData;

class Hreflangs implements ResolverInterface
{
    /**
     * @var HreflangsFactory
     */
    protected $hreflangsFactory;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Hreflangs constructor.
     *
     * @param HreflangsFactory $hreflangsFactory
     * @param PageRepositoryInterface $pageRepository
     * @param HelperData $helperData
     */
    public function __construct(
        HreflangsFactory $hreflangsFactory,
        PageRepositoryInterface $pageRepository,
        HelperData $helperData
    ) {
        $this->hreflangsFactory = $hreflangsFactory;
        $this->pageRepository   = $pageRepository;
        $this->helperData       = $helperData;
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
        $cmsPage = $this->pageRepository->getById($value[PageInterface::PAGE_ID]);

        return [
            'items' => $this->getHreflangsData($cmsPage),
        ];
    }

    /**
     * @param \Magento\Cms\Model\Page $cmsPage
     * @return array|null
     */
    protected function getHreflangsData(\Magento\Cms\Model\Page $cmsPage): ?array
    {
        $fullActionName = $this->isHomePage($cmsPage) ? 'cms_index_index' : 'cms_page_view';
        $arguments      = ['fullActionName' => $fullActionName];
        $hreflangModel  = $this->hreflangsFactory->create($fullActionName, $arguments);

        if (!$hreflangModel) {
            return null;
        }

        $hreflangModel->setEntity($cmsPage);

        $hreflangUrls = $hreflangModel->getHreflangUrls();

        if (!$hreflangUrls) {
            return null;
        }

        $data = [];

        foreach ($hreflangUrls as $code => $hreflangUrl) {
            $data[$code] = [
                'url'  => $hreflangUrl,
                'code' => $code
            ];
        }

        return $data;
    }

    /**
     * @param \Magento\Cms\Model\Page $page
     * @return bool
     */
    protected function isHomePage(\Magento\Cms\Model\Page $page): bool
    {
        $homePageId     = null;
        $homeIdentifier = $this->helperData->getHomeIdentifier();

        if (strpos($homeIdentifier, '|') !== false) {
            list($homeIdentifier, $homePageId) = explode('|', $homeIdentifier);
        }

        return $homeIdentifier == $page->getIdentifier();
    }
}
