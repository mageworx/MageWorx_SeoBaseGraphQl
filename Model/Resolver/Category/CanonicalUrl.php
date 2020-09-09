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

class CanonicalUrl implements ResolverInterface
{
    /**
     * @var \MageWorx\SeoBase\Model\CanonicalFactory
     */
    protected $canonicalFactory;

    /**
     * @var string
     */
    protected $fullActionName = 'catalog_category_view';

    /**
     * CanonicalUrl constructor.
     *
     * @param \MageWorx\SeoBase\Model\CanonicalFactory $canonicalFactory
     */
    public function __construct(
        \MageWorx\SeoBase\Model\CanonicalFactory $canonicalFactory
    ) {
        $this->canonicalFactory = $canonicalFactory;
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

        $category  = $value['model'];
        $arguments = ['fullActionName' => $this->fullActionName];

        /** @var \MageWorx\SeoBase\Model\CanonicalInterface $canonicalModel */
        $canonicalModel = $this->canonicalFactory->create($this->fullActionName, $arguments);
        $canonicalModel->setEntity($category);

        $canonicalUrl = $canonicalModel->getCanonicalUrl();

        if ($canonicalUrl) {
            return $canonicalUrl;
        }

        return null;
    }
}
