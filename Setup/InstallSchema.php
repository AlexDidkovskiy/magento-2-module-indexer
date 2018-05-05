<?php
namespace Echoecho\Indexer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use EchoEcho\Indexer\Model\ResourceModel\Indexer\Product\IsFragile;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $setup->startSetup();

        $this->createTableProductFragileIndexer($setup);
        $this->createTableProductFragileIndexer($setup, 'idx', false);

        $setup->endSetup();
    }

    /**
     * Creates table for product is_fragile index
     * @param SchemaSetupInterface $setup
     * @param string $suffix
     * @param bool $useIndexes
     */
    protected function createTableProductFragileIndexer(
        SchemaSetupInterface $setup,
        $suffix = '',
        $useIndexes = true
    ) {
        $connection = $setup->getConnection();
        $tableName = !empty($suffix) ? IsFragile::TABLE_NAME . "_$suffix" : IsFragile::TABLE_NAME;
        $tableNameTranslated = $setup->getTable($tableName);

        $table = $connection->newTable($tableNameTranslated);

        $table->addColumn(
            IsFragile::COLUMN_ENTITY_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Entity ID'
        );

        $table->addColumn(
            IsFragile::COLUMN_IS_FRAGILE,
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => true],
            'Is fragile'
        );

        if ($useIndexes) {
            $table->addIndex(
                $setup->getIdxName($tableName, [IsFragile::COLUMN_ENTITY_ID]),
                [IsFragile::COLUMN_ENTITY_ID]
            );

            $table->addForeignKey(
                $setup->getFkName(
                    $tableName,
                    IsFragile::COLUMN_ENTITY_ID,
                    'catalog_product_entity',
                    'entity_id'
                ),
                IsFragile::COLUMN_ENTITY_ID,
                $setup->getTable('catalog_product_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            );
        }

        $table->setComment('Catalog Product Fragile Index Table');

        $setup->getConnection()->createTable($table);
    }
}