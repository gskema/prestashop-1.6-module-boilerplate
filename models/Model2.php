<?php

/**
 * Class MyModule_Model2
 */
class MyModule_Model2 extends ObjectModel
{
    /** @var string */
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
    public $position = 0;

    /**
     * Model definition
     */
    public static $definition = array(
        'table'   => 'mm_model2',
        'primary' => 'id_mm_model2',
        'multilang' => true,
        'fields' => array(
            'property1' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128),
            'toggleable' => array('type' => self::TYPE_BOOL,),
            'active'    => array('type' => self::TYPE_BOOL,),
            'position'  => array('type' => self::TYPE_INT,),

            // Language fields
            'text'    => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'lang' => true),
            'content' => array('type' => self::TYPE_HTML,   'validate' => 'isCleanHtml',   'lang' => true),
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

        $position = Db::getInstance()->getValue($sql);

        // If value is null, then return -1 so we can increment it to -1 + 1 = 0
        return Tools::strlen($position) ? (int)$position : -1;
    }
}
