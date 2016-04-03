<?php

$installer = $this;
$installer->startSetup();



$table = $installer->getTable('barcode_import/pending');
 if ($installer->tableExists($table)) {
     $installer->getConnection()->dropTable($table);
 }


// Note no VARCHAR since they're deprecated in DDL Land.
$ddlTable = $installer->getConnection()->newTable($table)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
        'identity' => true,
    ), 'Type ID')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('nullable' => false,), 'Sku')
    ->addColumn('barcode', Varien_Db_Ddl_Table::TYPE_TEXT, null, array('nullable' => true,), 'Barcode')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Updated At')
;

$installer->getConnection()->createTable($ddlTable);
$installer->endSetup();