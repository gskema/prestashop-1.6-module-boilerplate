## ObjectModel

## ObjectModel MultiShop

To have a shop association table for an ObjectModel class, you must declare the association table
to PrestaShop before calling the class by calling :

``` php
Shop::addTableAssociation('my_shop_object_model', array('type' => 'shop'));

// or

Shop::addTableAssociation('my_translatable_shop_object_model', array('type' => 'shop'));
Shop::addTableAssociation('my_translatable_shop_object_model_lang', array('type' => 'fk_shop'));
```

## ObjectModel Instantiation

Arguments:
 - `id`
 - `id_lang`
 - `object`
 - `definition`
 - `id_shop`
 - `use_cache`

1. If `use_cache`, return cached version. Otherwise, compile object and return.
2. Select all fields from `table` (main table) table where `table.id = id`
3. If `def['multilang']`, select all fields from `table_lang` table where `table_lang.id = table.id AND table_lang.id_lang = id_lang`
4. If `def['multilang']` and `def['multilang_shop']`, add additional condition: `where table_lang.id_shop = id_shop`
5. If table is associated to shop `Shop::isTableAssociated`, then adds shop condition `where table_shop.id_shop = id_shop`.
   Association must be added before calling any object constructors. `Shop::addTableAssociation('table', ['type' => 'shop'])`
6. If `id_lang < 1` and `def['multilang']`, then get all rows from `table_lang`.
7. If `def['multilang']` and `def['multilang_shop']`, add additional condition: `where table_lang.id_shop = id_shop`
8. Foreach `table_lang` columns, create `object->{column_name} = []`, and puts array or translations `[1 => Lithuanian, 2 => English]`
9. Set object ID `object->id = id`
10. Foreach columns from step `2.`, write values to `object->{column_name} = {value}`

## ObjectModel Insertion

1. Before add hooks
2. Automatically set date fields
3. If table is associated to shops `Shop::isTableAssociated`, then get context shop IDs (list)
4. Find default shop ID
5. Insert values to primary table `table`, values are `$this->getFields()`
6. Get ID set ID `object->id = id`
7. If `Shop::isTableAssociated`, the foreach `id_shop` Insert `id, id_shop` and `$this->getFieldsShop()` to `table_shop`


## ObjectModel Definition

``` php

$definition['table']
  Primary table name without the PrestaShop table prefix. E.g.: 'product'

$definition['primary']
  Primary key column name. Ideally, shoud be: 'id_' + $definition['table']

$definition['multilang']
  Type: boolean
  Set to true when you have translatable fields that are saved in $definition['table'] + '_lang' table

$definition['multilang_shop']
  Type: boolean
  Set to true when you have translatable fields that are saved in $definition['table'] + '_lang' table
  and the table has id_shop column, meaning translations are different for each shop.

$definition['fields']
  Type: array, with string keys
  String keys are field names. E.g. 'price'. Each array item is an array with field properties.
  E.g.:
  $definition['fields'] = array(
    'price' => array('type' => ObjectModel::TYPE_FLOAT, 'validate' => 'isFloat'),
    'name'  => array('type' => ObjectModel::TYPE_STRING, 'validate' => 'isName'),
  );

$definition['fields'][]
  Key: string
  Type: array, with string keys
  Key is a field name. Must match column name in the database table. The value is array of fields properties.
  E.g.:
  'price' => array('type' => ObjectModel::TYPE_FLOAT, 'validate' => 'isFloat'),

$definition['fields'][]['type']
  Type: int
  Values:

    Value: ObjectModel::TYPE_INT
    Formatter:
      return (int)$value;

    Value: ObjectModel::TYPE_BOOL
    Formatter:
      return (int)$value;

    Value: ObjectModel::TYPE_FLOAT
    Formatter:
      return (float)str_replace(',', '.', $value);

    Value: ObjectModel::TYPE_DATE
    Formatter:
      if (!$value) return '0000-00-00';
      if ($with_quotes) return '\''.pSQL($value).'\'';
      return pSQL($value);

    Value: ObjectModel::TYPE_HTML
    Formatter:
      if ($purify) $value = Tools::purifyHTML($value);
      if ($with_quotes) return '\''.pSQL($value, true).'\'';
      return pSQL($value, true);

    Value: ObjectModel::TYPE_SQL
    Formatter:
      if ($with_quotes) return '\''.pSQL($value, true).'\'';
      return pSQL($value, true);

    Value: ObjectModel::TYPE_SQL
    Formatter:
      return $value;

    Value: ObjectModel::TYPE_STRING
    Formatter:
      if ($with_quotes) return '\''.pSQL($value).'\'';
      return pSQL($value);

    Value: 0 (property does not exists)
    Formatter:
      if ($with_quotes) return '\''.pSQL($value).'\'';
      return pSQL($value);

$definition['fields'][]['validate']
  Type: string
  Values: Any method name from Validate class.
  If property does not exist, validation will not be called.
  E.g.
  'validate' => 'isEmail' // Validate::isEmail

$definition['fields'][]['required']
  Type: boolean
  If true, the field will be required, empty values will not pass validation.

$definition['fields'][]['lang']
  Type: boolean
  If true, indicates that the value for this fields is in $definition['table'] + '_lang' table.

$definition['fields'][]['size']
  Type: integer or array
  Validates value length against the given range.
  E.g.
  'size' => 5                              // Translates into: array('min' => 0, 'max' => 5)
  'size' => array('min' => 5, 'max' => 10) // Translates into: array('min' => 5, 'max' => 10)

$definition['fields'][]['copy_post']
  Type: boolean
  If enabled, skip writing field from POST to object in ObjectModel::validateController.
  Mostly used in opc, auth, address, identity controllers. Do not use.

$definition['fields'][]['allow_null']
  Type: boolean
  Effects: unknown. Do not use.

$definition['fields'][]['shop']
  Type: boolean
  Values: true / '1' / 'both'
  If true, insert the field into $definition['table'] + '_shop' table.

$definition['fields'][]['values']
  Type: array
  Limits the valid values to a finite set.
  E.g.
  'values' => array('both', 'catalog', 'search', 'none'),

$definition['fields'][]['default']
  Type: array
  Specifies the default value. Used when the obejct value evaluates to false: !$value
  E.g.
  'default' => 'both'
```
