<?php

use \MyModule\Core\ObjectModel;
use \MyModule\Model1\Collection as Model1Collection;

/**
 * Class MyModule_Model1
 */
class MyModule_Model1 extends ObjectModel
{
    /** @var string */
    public $property1;

    /** @var bool */
    public $toggleable = true;

    /** @var bool */
    public $active = true;

    /** @var int */
    public $position = 0;

    /**
     * Model definition
     */
    public static $definition = array(
        'table'   => 'mm_model1',
        'primary' => 'id_mm_model1',
        'fields'  => array(
            'property1'  => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'length' => 128),
            'toggleable' => array('type' => self::TYPE_BOOL,),
            'active'     => array('type' => self::TYPE_BOOL,),
            'position'   => array('type' => self::TYPE_INT,),
        ),
    );

    public function add($autoDate = true, $nullValues = false)
    {
        $this->position = self::getMaxPosition() + 1;
        return parent::add($autoDate, $nullValues);
    }

    public static function getMaxPosition()
    {
        $sql = new DbQuery();
        $sql->select('MAX(position)');
        $sql->from(self::$definition['table']);

        $position = (string)Db::getInstance()->getValue($sql);

        // If value is null, then return -1 so we can increment it to -1 + 1 = 0
        return Tools::strlen($position) ? (int)$position : -1;
    }

    /**
     * @return Model1Collection
     */
    public static function query()
    {
        return new Model1Collection();
    }
}
