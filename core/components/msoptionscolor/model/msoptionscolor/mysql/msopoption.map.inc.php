<?php
$xpdo_meta_map['msopOption']= array (
  'package' => 'msoptionscolor',
  'version' => '1.1',
  'table' => 'msop_option',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'name' => NULL,
    'key' => NULL,
    'description' => NULL,
    'rank' => 0,
    'active' => 1,
    'editable' => 1,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
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
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => true,
      'default' => 1,
    ),
    'editable' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => true,
      'default' => 1,
    ),
  ),
  'aggregates' => 
  array (
    'Color' => 
    array (
      'class' => 'msopColor',
      'local' => 'id',
      'foreign' => 'option',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
