<?php
$xpdo_meta_map['msopColor']= array (
  'package' => 'msoptionscolor',
  'version' => '1.1',
  'table' => 'msop_color',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'product_id' => NULL,
    'option' => NULL,
    'value' => NULL,
    'color' => '0',
    'pattern' => NULL,
    'active' => 1,
    'rank' => 0,
  ),
  'fieldMeta' => 
  array (
    'product_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'option' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
    ),
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'color' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '11',
      'phptype' => 'string',
      'null' => true,
      'default' => '0',
    ),
    'pattern' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => true,
      'default' => 1,
    ),
    'rank' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'product' => 
    array (
      'alias' => 'product',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'product_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'option' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'value' => 
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
    'Option' => 
    array (
      'class' => 'msopOption',
      'local' => 'option',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Ral' => 
    array (
      'class' => 'msopRal',
      'local' => 'color',
      'foreign' => 'html',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
