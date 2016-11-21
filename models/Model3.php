<?php

use \MyModule\Core\ObjectModel;

/**
 * Class MyModule_Model3
 *
 * @TODO Associations must be set once before instantiating MyModule_Model4
 * @TODO Shop::addTableAssociation('mm_model3', ['type' => 'shop']);
 */
class MyModule_Model3 extends ObjectModel
{
    /** @var string */
    public $property1;

    /** @var bool */
    public $active = true;

    /** @var int */
    public $position;

    /** @var bool */
    public $toggleable = true;

    /**
     * Model definition
     */
    public static $definition = [
        'table'   => 'mm_model3',
        'primary' => 'id_mm_model3',
        'fields' => [
            'property1'  => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'length' => 128,],
            'toggleable' => ['type' => self::TYPE_BOOL,],
            'active'     => ['type' => self::TYPE_BOOL,],

            // Shop fields
            // @see Object::formatFields uses incorrect comparison operator for $data['shop'] != 'both'
            // That why we 'hack' it using 'shop' => 1
            'position' => ['type' => self::TYPE_INT, 'shop' => '1',],
        ],
    ];

    /**
     * Inserts new item to the database
     *
     * @param bool $autoDate
     * @param bool $nullValues
     *
     * @return bool
     * @throws PrestaShopException
     */
    public function add($autoDate = true, $nullValues = false)
    {
        $status = parent::add($autoDate, $nullValues);

        // @TODO Try using simple assignment $this->position =, before adding
        if ($status) {
            $id_shop_list = Shop::getContextListShopID();
            if (!empty($this->id_shop_list)) {
                $id_shop_list = $this->id_shop_list;
            }

            // @TODO Fix initial value
            foreach ($id_shop_list as $id_shop) {
                Db::getInstance()->update(
                    self::$definition['table'],
                    [
                        'position' => self::getMaxPosition($id_shop) + 1
                    ],
                    self::$definition['primary'].' = '.(int)$this->id.' AND id_shop = '.(int)$id_shop
                );
            }
        }

        return $status;
    }

    public static function getMaxPosition($id_shop)
    {
        $sql = new DbQuery();
        $sql->select('MAX(position)');
        $sql->from(self::$definition['table']);
        $sql->where('id_shop = '.(int)$id_shop);

        $position = (string)Db::getInstance()->getValue($sql);

        // @TODO If value is null, then return -1 so we can increment it to -1 + 1 = 0
        return Tools::strlen($position) ? (int)$position : -1;
    }
}
