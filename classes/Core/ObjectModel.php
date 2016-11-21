<?php

namespace MyModule\Core;

/**
 * Class ObjectModel
 */
class ObjectModel extends \ObjectModel
{
    /**
     * Finds and object by ID or returns a new one
     *
     * @param int $id
     *
     * @return static
     */
    public static function find($id)
    {
        return new static($id);
    }

    /**
     * Saves object to database.
     *
     * @param bool $nullValues
     * @param bool $autoDate
     *
     * @return bool
     */
    public function save($nullValues = true, $autoDate = true)
    {
        // Make sure that NULL values are allowed, since we will be using them a lot.
        return parent::save(true, true);
    }

    /**
     * Adds object to database.
     *
     * @param bool $autoDate
     * @param bool $nullValues
     *
     * @return bool
     */
    public function add($autoDate = true, $nullValues = true)
    {
        // Make sure that NULL values are allowed, since we will be using them a lot.
        return parent::add(true, true);
    }

    /**
     * Updates object in the database.
     *
     * @param bool $nullValues
     *
     * @return bool
     */
    public function update($nullValues = true)
    {
        // Make sure that NULL values are allowed, since we will be using them a lot.
        return parent::update(true);
    }

    /**
     * Returns an array of formatted object field values.
     *
     * @return array
     */
    public function toArray()
    {
        $id = (int)$this->id;

        $values = [
            $this->def['primary'] => $id > 0 ? $id : null,
        ];

        foreach (static::$definition['fields'] as $property => $def) {
            $values[$property] = static::formatFieldValue($property, $this->{$property});
        }

        return $values;
    }

    /**
     * Formats a value to defined type in model definition.
     *
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     */
    public static function formatFieldValue($field, $value)
    {
        if (!isset(static::$definition['fields'][$field])) {
            return $value;
        }

        if (null === $value && !empty(static::$definition['fields'][$field]['allow_null'])) {
            return null;
        }

        if (empty(static::$definition['fields'][$field]['type'])) {
            return $value;
        }

        switch (static::$definition['fields'][$field]['type']) {
            case self::TYPE_INT:
                return intval($value);
            case self::TYPE_BOOL:
                return boolval($value);
            case self::TYPE_FLOAT:
                return floatval($value);
            default:
                return strval($value);
        }
    }
}
