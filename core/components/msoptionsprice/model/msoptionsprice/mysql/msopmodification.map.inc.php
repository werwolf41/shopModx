<?php
$xpdo_meta_map['msopModification']= array (
  'package' => 'msoptionsprice',
  'version' => '1.1',
  'table' => 'msop_modifications',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'rid' => 0,
    'type' => 1,
    'price' => '0',
    'article' => '',
    'weight' => 0.0,
    'count' => 0,
    'image' => NULL,
    'active' => 1,
    'rank' => 0,
  ),
  'fieldMeta' => 
  array (
    'rid' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 0,
    ),
    'type' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '3',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
    ),
    'price' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '11',
      'phptype' => 'string',
      'null' => false,
      'default' => '0',
    ),
    'article' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '50',
      'phptype' => 'varchar',
      'null' => false,
      'default' => '',
    ),
    'weight' => 
    array (
      'dbtype' => 'decimal',
      'precision' => '13,3',
      'phptype' => 'float',
      'null' => false,
      'default' => 0.0,
    ),
    'count' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'image' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
    'rank' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'image' => 
    array (
      'alias' => 'image',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'image' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Options' => 
    array (
      'class' => 'msopModificationOption',
      'local' => 'id',
      'foreign' => 'mid',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
