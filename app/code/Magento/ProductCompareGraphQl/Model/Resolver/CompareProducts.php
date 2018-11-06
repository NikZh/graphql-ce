<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductCompareGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection as CompareProductCollection;
use Magento\Catalog\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ProductCompareGraphQl\Model\VisitorDataProvider;

class CompareProducts implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Config
     */
    private $catalogConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var VisitorDataProvider
     */
    private $visitorDataProvider;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Config $catalogConfig
     * @param StoreManagerInterface $storeManager
     * @param VisitorDataProvider $visitorDataProvider
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Config $catalogConfig,
        StoreManagerInterface $storeManager,
        VisitorDataProvider $visitorDataProvider
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->catalogConfig = $catalogConfig;
        $this->storeManager = $storeManager;
        $this->visitorDataProvider = $visitorDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if ($field->getName() == 'compareProducts') {
            return [];
        }
        //TODO: need to implement compare products provider
        $collection = $this->collectionFactory->create();
        $collection->useProductItem(true)->setStoreId($this->storeManager->getStore()->getId());
        $collection->addAttributeToSelect($this->catalogConfig->getProductAttributes());
        $collection->loadComparableAttributes();
        $this->addCustomerFilter($collection);
        $items = [];
        foreach ($collection as $item) {
            $productData = $item->getData();
            $productData['model'] = $item;
            $items[] = [
                'item_id' => $item->getData('catalog_compare_item_id'),
                'product' => $productData
            ];
        }

        return $items;
    }

    /**
     * Add customer filter to compare product collection
     *
     * @param CompareProductCollection $collection
     */
    private function addCustomerFilter(CompareProductCollection $collection)
    {
        $customerId = $this->visitorDataProvider->getCustomerId();
        if ($customerId) {
            $collection->setCustomerId($customerId);
        } else {
            $collection->setVisitorId($this->visitorDataProvider->getVisitorId());
        }
    }
}
