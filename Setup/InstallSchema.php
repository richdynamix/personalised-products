<?php

namespace Richdynamix\PersonalisedProducts\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('rp_export_products'))
            ->addColumn(
                'increment_id',
                Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Increment ID'
            )
            ->addColumn('product_id', Table::TYPE_INTEGER, 11, ['nullable' => false])
            ->addColumn('is_exported', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default' => '1'], 'Has the product been exported?')
            ->addColumn('creation_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Creation Time')
            ->addColumn('update_time', Table::TYPE_DATETIME, null, ['nullable' => false], 'Update Time')
            ->setComment('Personalised Products export to PredictionIO');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}