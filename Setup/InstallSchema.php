<?php
namespace Echoecho\Indexer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

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

        $setup->endSetup();
    }

    /**
     * Creates table for product is_fragile index
     * @param SchemaSetupInterface $setup
     */
    protected function createTableProductFragileIndexer(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $tableName = 'echoecho_product_fragile_index';
        $tableNameTranslated = $setup->getTable($tableName);

        $table = $connection->newTable($tableNameTranslated);

        $table->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Entity ID'
        );

        $table->addColumn(
            'is_fragile',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => true],
            'Is fragile'
        );

        $table->addIndex(
            $setup->getIdxName($tableName, ['entity_id']),
            ['entity_id']
        );

        $table->addForeignKey(
            $setup->getFkName(
                $tableName,
                'entity_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'entity_id',
            $setup->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $table->setComment('Catalog Product Fragile Index Table');

        $setup->getConnection()->createTable($table);
    }
}