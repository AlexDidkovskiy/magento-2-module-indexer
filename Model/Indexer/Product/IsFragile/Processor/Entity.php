<?php
namespace EchoEcho\Indexer\Model\Indexer\Product\IsFragile\Processor;

use EchoEcho\Indexer\Model\Indexer\Product\IsFragile\AbstractProcessor;
use Magento\Catalog\Api\Data\ProductInterface;

class Entity extends AbstractProcessor
{
    /**
     * @param ProductInterface $product
     */
    public function process(ProductInterface $product)
    {
        $this->reindexRow($product->getId());
    }

    /**
     * @param int $id
     */
    public function deleteIndex(int $id)
    {
        $this->reindexRow($id);
    }
}