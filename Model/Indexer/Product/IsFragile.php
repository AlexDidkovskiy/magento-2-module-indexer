<?php
namespace EchoEcho\Indexer\Model\Indexer\Product;

use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use EchoEcho\Indexer\Model\ResourceModel\Indexer\Product\IsFragile as ResourceIndexer;

class IsFragile implements IndexerActionInterface, MviewActionInterface
{
    const ERROR_EMPTY_LIST_OF_INDEXED_PRODUCTS = "Empty list of indexed products";
    const ERROR_EMPTY_INDEXED_PRODUCT_ID = "Empty indexed product id";

    /**
     * @var ResourceIndexer $resourceIndexer
     */
    protected $resourceIndexer;

    /**
     * @param ResourceIndexer $resourceIndexer
     */
    public function __construct(ResourceIndexer $resourceIndexer)
    {
        $this->resourceIndexer = $resourceIndexer;
    }

    /**
     * @inheritdoc
     */
    public function executeFull() {
        $this->resourceIndexer->reindexAll();
    }

    /**
     * @inheritdoc
     */
    public function executeList(array $ids = [])
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException(__(self::ERROR_EMPTY_LIST_OF_INDEXED_PRODUCTS));
        }

        $this->resourceIndexer->reindex($ids);
    }

    /**
     * @inheritdoc
     */
    public function executeRow($id = null)
    {
        if (empty($id)) {
            throw new \InvalidArgumentException(__(self::ERROR_EMPTY_INDEXED_PRODUCT_ID));
        }
        $this->resourceIndexer->reindex([$id]);
    }

    /**
     * @inheritdoc
     */
    public function execute($ids)
    {
        $this->resourceIndexer->reindex($ids);
    }
}