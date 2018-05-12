<?php
namespace EchoEcho\Indexer\Plugin\Magento\CatalogSearch\Model\Layer\Category;

use Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider as Subject;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ItemCollectionProvider
{
    public function afterGetCollection(Subject $subject, Collection $collection)
    {
        $collection->addAttributeToSelect('created_at');
        $collection->addAttributeToSelect('is_fragile');
        $collection->addAttributeToSelect('erin_recommends');

        $a = $collection->getSelect()->assemble();


        return $collection;
    }
}