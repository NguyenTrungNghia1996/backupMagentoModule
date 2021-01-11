<?php


namespace Report\Order\Setup;


use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        //$quote = 'quote';
        $orderTable = 'sales_order';

        //Order Grid table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'sales_representative',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'comment' =>'Sales Representative Name'
                ]
            );

        $setup->endSetup();
    }
}
