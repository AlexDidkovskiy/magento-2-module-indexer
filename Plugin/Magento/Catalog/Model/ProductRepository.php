<?php
namespace Echoecho\Indexer\Plugin\Magento\Catalog\Model;

use Magento\Catalog\Api\Data\ProductSearchResultsInterface;
use Magento\Catalog\Model\ProductRepository as Subject;
use Magento\Catalog\Api\Data\ProductInterface;
use EchoEcho\Indexer\Model\IsFragile;

/**
 * Class ProductRepository
 */
class ProductRepository
{
    /**
     * @param ProductInterface $product
     * @param bool $saveOptions
     * @return array
     */
    public function beforeSave(ProductInterface $product, $saveOptions = false)
    {
        $extensionAttributes = $product->getExtensionAttributes();
        if (!$extensionAttributes) {
            return [$product, $saveOptions];
        }
        $product->setIsFragile(
            $extensionAttributes->getIsFragile()
        );

        return [$product, $saveOptions];
    }

    /**
     * @param Subject $subject
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function afterGetById(Subject $subject, ProductInterface $product)
    {
        return $this->addExtensionAttribute($product);
    }

    /**
     * @param Subject $subject
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function afterGet(Subject $subject, ProductInterface $product)
    {
        return $this->addExtensionAttribute($product);
    }

    /**
     * @param Subject $subject
     * @param ProductSearchResultsInterface $searchResults
     * @return ProductSearchResultsInterface
     */
    public function afterGetList(Subject $subject, ProductSearchResultsInterface $searchResults)
    {
        $newItems = [];
        foreach ($searchResults->getItems() as $product) {
            $newItems[] = $this->addExtensionAttribute($product);
        }
        $searchResults->setItems($newItems);

        return $searchResults;
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     */
    private function addExtensionAttribute(ProductInterface $product)
    {
        $extensionAttributes = $product->getExtensionAttributes();

        $extensionAttributes->setIsFragile(
            $product->getData(IsFragile::IS_FRAGILE)
        );

        $product->setExtensionAttributes($extensionAttributes);

        return $product;
    }
}