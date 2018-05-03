<?php
namespace Echoecho\Indexer\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Product;
use EchoEcho\Indexer\Model\IsFragile;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->addFragileAttribute($setup);

        $setup->endSetup();
    }

    /**
     * Adds extension attribute "fragile" to Product
     * @param ModuleDataSetupInterface $setup
     */
    protected function addFragileAttribute(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $entityType = $eavSetup->getEntityTypeId(Product::ENTITY);

        $attributeCode = IsFragile::IS_FRAGILE;

        $attributeProperties = [
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'label'  => IsFragile::IS_FRAGILE,
            'type' => 'int',
            'input' => 'boolean',
            'visible_on_front' => true,
            'used_in_product_listing' => true,
            'searchable' => true,
            'required' => false,
            'sort_order' => 50,
            'apply_to' => '',
        ];

        $eavSetup->addAttribute($entityType, $attributeCode, $attributeProperties);
    }
}
