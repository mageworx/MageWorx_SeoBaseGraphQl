<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoBaseGraphQl\Model\Resolver\Category;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\SeoBase\Model\HreflangsFactory;

class Hreflangs implements ResolverInterface
{
    /**
     * @var HreflangsFactory
     */
    protected $hreflangsFactory;

    /**
     * @var string
     */
    protected $fullActionName = 'catalog_category_view';

    /**
     * Hreflangs constructor.
     *
     * @param HreflangsFactory $hreflangsFactory
     */
    public function __construct(HreflangsFactory $hreflangsFactory)
    {
        $this->hreflangsFactory = $hreflangsFactory;
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
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        return [
            'items' => $this->getHreflangsData($value['model']),
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return array|null
     */
    protected function getHreflangsData(\Magento\Catalog\Model\Category $category): ?array
    {
        $arguments     = ['fullActionName' => $this->fullActionName];
        $hreflangModel = $this->hreflangsFactory->create($this->fullActionName, $arguments);

        if (!$hreflangModel) {
            return null;
        }

        $hreflangModel->setEntity($category);

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
}
