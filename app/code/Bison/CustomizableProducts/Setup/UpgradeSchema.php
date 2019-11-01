<?php

namespace Bison\CustomizableProducts\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.3.0') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('bison_user_logo')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Entity ID'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                6,
                ['nullable' => false, 'default' => 0],
                'Customer ID'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false,],
                'File Name'
            )->addColumn(
                'orig_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false,],
                'Original File Name'
            );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.4.0') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('bison_inspirations')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Entity ID'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                6,
                ['nullable' => false, 'default' => 0],
                'Product ID'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false,],
                'File Name'
            );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.5.0') < 0) {

            $installer = $setup;
            $installer->startSetup();

            $table = $installer->getConnection()->newTable(
                $installer->getTable('bison_user_design')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true,],
                'Entity ID'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                9,
                ['nullable' => false, 'default' => 0],
                'Customer ID'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false,],
                'File Name'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                9,
                ['nullable' => false, 'default' => 0],
                'Product ID'
            );

            $installer->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '1.6.0') < 0) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('customizer_logo_category')
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Entity ID'
            )->addColumn(
                'parent_category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                6,
                ['nullable' => false, 'default' => 0],
                'Parent category Id'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false,],
                'Category Name'
            );

            $installer->getConnection()->createTable($table);

            $table = $installer->getConnection()->newTable(
                $installer->getTable('customizer_predefined_logo')
            )->addColumn(
                'logo_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
                'Logo ID'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                6,
                ['nullable' => false, 'default' => 0],
                'category Id'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false,],
                'Name'
            )->addColumn(
                'filename',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false,],
                'File name'
            );

            $installer->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }
}

