<?php
namespace EchoEcho\Indexer\Model\ResourceModel\Indexer\Product;

use Magento\Catalog\Model\ResourceModel\Product\Indexer\AbstractIndexer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Indexer\Table\StrategyInterface;
use Magento\Eav\Model\Config;

use EchoEcho\Indexer\Model\IsFragile as IsFragileAttribute;

class IsFragile extends AbstractIndexer
{
    const TABLE_NAME = "echoecho_product_fragile_index";

    const COLUMN_ENTITY_ID = 'entity_id';
    const COLUMN_IS_FRAGILE = 'is_fragile';

    const BATCH_SIZE = 500;

    const ERROR_EMPTY_LIST_OF_IDS = "Empty input array of IDs";

    /**
     * @inheritdoc
     */
    public function __construct(
        Context $context,
        StrategyInterface $tableStrategy,
        Config $eavConfig,
        $connectionName = null
    ){
        parent::__construct($context, $tableStrategy, $eavConfig, $connectionName);
    }

    /**
     * Initiate resource
     */
    protected function _construct()
    {
        $this->_init(static::TABLE_NAME, static::COLUMN_ENTITY_ID);
    }

    /**
     * Reindexes all products
     * @return $this
     * @throws \Exception
     */
    public function reindexAll()
    {
        $this->tableStrategy->setUseIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $this->buildIndex($this->getIdxTable());
            $this->syncData();
            $this->commit();
        } catch (\Exception $exception) {
            $this->rollBack();
            throw $exception;
        }

        return $this;
    }

    /**
     * Reindexes a number of products in batches
     * @param array $ids
     * @return $this
     * @throws LocalizedException
     */
    public function reindex(array $ids = [])
    {
        if (empty($ids)) {
            throw new LocalizedException(
                new Phrase(static::ERROR_EMPTY_LIST_OF_IDS)
            );
        }
        $idsBatches = array_chunk($ids, static::BATCH_SIZE);
        foreach ($idsBatches as $idsBatch) {
            $this->processSingleBatch($idsBatch);
        }
        return $this;
    }

    /**
     * Processes single batch of products
     * @param array $ids
     * @return bool
     * @throws \Exception
     */
    private function processSingleBatch(array $ids) {
        $this->beginTransaction();
        try {
            $this->deleteIndex($this->getMainTable(), $ids);
            $this->buildIndex($this->getMainTable(), $ids);
            $this->commit();
        } catch (\Exception $exception) {
            $this->rollBack();
            throw $exception;
        }
        return true;
    }

    /**
     * Deletes products from index
     * @param $destinationTable
     * @param array $ids
     */
    private function deleteIndex($destinationTable, $ids = [])
    {
        $connection = $this->getConnection();
        $condition = $connection->prepareSqlCondition(
            static::COLUMN_ENTITY_ID,
            ['in' => $ids]
        );

        $connection->delete($destinationTable, $condition);
    }

    /**
     * Insert new data into index table using select
     * @param $destinationTable
     * @param array $ids
     */
    private function buildIndex($destinationTable, $ids = [])
    {
        $productTable = 'catalog_product_entity';
        $attributeTable = 'catalog_product_entity_int';
        $attribute = $this->_getAttribute(IsFragileAttribute::IS_FRAGILE);
        $attributeId = $attribute->getId();

        $select = $this->getConnection()->select();

        $select
            ->from(
                [$productTable => $this->getTable($productTable)],
                ['entity_id']
            )
            ->joinLeft(
                [$attributeTable => $this->getTable($attributeTable)],
                implode(' AND ', [
                    "$productTable.entity_id = $attributeTable.entity_id",
                    "$attributeTable.attribute_id = $attributeId"
                ]),
                ['value']
            )
            ->order("$productTable.entity_id ASC")
            ->distinct(true);

        if (!empty($ids)) {
            $select->where("$attributeTable.entity_id IN (?)", $ids);
        }

        $this->insertFromSelect($select, $destinationTable, []);
    }
}