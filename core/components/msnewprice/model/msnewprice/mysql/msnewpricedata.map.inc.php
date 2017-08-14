<?php
$xpdo_meta_map['msnewpricedata']= array (
  'package' => 'msnewprice',
  'version' => '1.1',
  'table' => 'msnewprice_data',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'msn_product_id' => NULL,
    'msn_newprice' => 0.0,
    'msn_action' => 0,
    'msn_overwrite' => 0,
    'msn_description' => NULL,
    'msn_startdate' => '0000-00-00 00:00:00',
    'msn_stopdate' => '0000-00-00 00:00:00',
    'msn_processed' => 0,
  ),
  'fieldMeta' => 
  array (
    'msn_product_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
    ),
    'msn_newprice' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '12,2',
      'phptype' => 'float',
      'null' => true,
      'default' => 0.0,
    ),
    'msn_action' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
    ),
    'msn_overwrite' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
    ),
    'msn_description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'msn_startdate' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
      'default' => '0000-00-00 00:00:00',
    ),
    'msn_stopdate' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
      'default' => '0000-00-00 00:00:00',
    ),
    'msn_processed' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => true,
      'default' => 0,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'product' => 
    array (
      'alias' => 'product',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'msn_product_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Product' => 
    array (
      'class' => 'msProduct',
      'local' => 'msn_product_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Resource' => 
    array (
      'class' => 'modResource',
      'local' => 'msn_product_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
