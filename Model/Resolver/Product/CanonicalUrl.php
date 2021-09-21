<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SeoBaseGraphQl\Model\Resolver\Product;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\Exception\LocalizedException;

class CanonicalUrl implements ResolverInterface
{
    /**
     * @var \MageWorx\SeoBase\Model\CanonicalFactory
     */
    protected $canonicalFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var string
     */
    protected $fullActionName = 'catalog_product_view';

    /**
     * CanonicalUrl constructor.
     *
     * @param \MageWorx\SeoBase\Model\CanonicalFactory $canonicalFactory
     */
    public function __construct(
        \MageWorx\SeoBase\Model\CanonicalFactory $canonicalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->canonicalFactory = $canonicalFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|null
     * @throws LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product   = $value['model'];
        $arguments = ['fullActionName' => $this->fullActionName];

        /** @var \MageWorx\SeoBase\Model\CanonicalInterface $canonicalModel */
        $canonicalModel = $this->canonicalFactory->create($this->fullActionName, $arguments);
        $canonicalModel->setEntity($product);
        $canonicalUrl = $canonicalModel->getCanonicalUrl();

        $canonicalStoreId = $canonicalModel->getCanonicalStoreId($product->getId());

        $result = [
            'url' => $canonicalUrl ?: null,
            'code' => $canonicalStoreId ? $this->storeManager->getStore($canonicalStoreId)->getCode() : ''
        ];

        return $result;
    }
}
