<?php
namespace EchoEcho\Indexer\Plugin\Magento\Catalog\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\Product as Subject;
use Magento\Catalog\Api\Data\ProductInterface;
use Closure;
use EchoEcho\Indexer\Model\Indexer\Product\IsFragile\Processor\Entity as EntityProcessor;

class Product
{
    /**
     * @var EntityProcessor
     */
    protected $entityProcessor;

    /**
     * Product constructor.
     * @param EntityProcessor $entityProcessor
     */
    public function __construct(EntityProcessor $entityProcessor)
    {
        $this->entityProcessor = $entityProcessor;
    }

    /**
     * Plugin for updating index after product save
     * @param Subject $subject
     * @param Closure $proceed
     * @param ProductInterface $product
     * @return mixed
     */
    public function aroundSave(
        Subject $subject,
        Closure $proceed,
        ProductInterface $product
    ) {
        $result = $proceed($product);
        $this->entityProcessor->process($product);

        return $result;
    }

    /**
     * Plugin for updating index after product delete
     * @param Subject $subject
     * @param Closure $proceed
     * @param ProductInterface $product
     * @return mixed
     */
    public function aroundDelete(
        Subject $subject,
        Closure $proceed,
        ProductInterface $product
    ) {
        $id = $product->getId();
        $result = $proceed($product);
        $this->entityProcessor->deleteIndex($id);

        return $result;
    }
}