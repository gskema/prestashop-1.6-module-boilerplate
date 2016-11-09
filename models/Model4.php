<?php

/**
 * Class MyModule_Model4
 *
 * @TODO Associations must be set once before instantiating MyModule_Model4
 * @TODO Shop::addTableAssociation('mm_model4', array('type' => 'shop'));
 * @TODO Shop::addTableAssociation('mm_model4_lang', array('type' => 'fk_shop'));
 */
class MyModule_Model4 extends ObjectModel
{
    /* @var string */
    public $property1;

    /** @var string */
    public $text;

    /** @var string */
    public $content;

    /** @var bool */
    public $toggleable = true;

    /** @var bool */
    public $active = true;

    /** @var int */
    public $position;

    /** @var array */
    public static $definition = array(
        'table'     => 'mm_model4',
        'primary'   => 'id_mm_model4',
        'multilang' => true,

        // @TODO Use when you have `_lang` table and `id_shop` column exists - translations are different for each shop
        // @TODO Shop::addTableAssociation('mm_model4_lang', array('type' => 'fk_shop'));
        'multilang_shop' => true,

        'fields' => array(
            'property1'  => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'toggleable' => array('type' => self::TYPE_BOOL,),
            'active'     => array('type' => self::TYPE_BOOL,),

            // Language fields
            'text'    => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true),
            'content' => array('type' => self::TYPE_HTML,   'validate' => 'isCleanHtml',   'lang' => true),

            // Shop fields
            // @see Object::formatFields uses incorrect comparison operator for $data['shop'] != 'both'
            // That why we 'hack' it using 'shop' => 1
            'position' => array('type' => self::TYPE_INT, 'shop' => '1'),
        ),
    );

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
                    array(
                        'position' => self::getMaxPosition($id_shop) + 1
                    ),
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

        $position = Db::getInstance()->getValue($sql);

        // If value is null, then return -1 so we can increment it to -1 + 1 = 0
        return Tools::strlen($position) ? (int)$position : -1;
    }
}
