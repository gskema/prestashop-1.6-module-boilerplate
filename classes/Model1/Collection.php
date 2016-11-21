<?php

namespace MyModule\Model1;

use \Db;
use \DbQuery;
use \MyModule\Core\Collection as BaseCollection;
use \MyModule_Model1 as Model1;

/**
 * Class Collection
 * @package MyModule\ObjectModel
 */
class Collection extends BaseCollection
{
    /** @var array */
    protected $where = [];

    /** @var array */
    protected $orderBy = [];

    /** @var int */
    protected $limit = null;

    /** @var array */
    protected $offset = null;

    /** @var Db */
    protected $db;

    /**
     * Hydrates a new collection from database rows
     *
     * @param array $rows
     *
     * @return static
     */
    public static function hydrate(array $rows)
    {
        return new static(array_map(function ($row) {
            return (new Model1())->hydrate($row);
        }, $rows));
    }

    /**
     * Outputs collection as a serializable array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->map(function ($model1) {
            /** @var Model1 $model1 */
            return $model1->toArray();
        });
    }

    /**
     * @param string $column
     * @param string $op
     * @param mixed  $value
     *
     * @return $this
     */
    public function where($column, $op, $value)
    {
        $this->where[] = sprintf(
            '%s %s %s',
            pSQL($column),
            pSQL($op),
            Model1::formatValue($value, Model1::$definition['fields'][$column]['type'], true)
        );
        return $this;
    }

    /**
     * @param string $column
     * @param string $way
     *
     * @return $this
     */
    public function orderBy($column, $way)
    {
        $this->orderBy[] = sprintf(
            '%s %s',
            pSQL($column),
            strtolower($way) === 'desc' ? 'DESC' : 'ASC'
        );
        return $this;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function limit($number)
    {
        $this->limit = max(0, (int)$number);
        return $this;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function offset($number)
    {
        $this->offset = max(0, (int)$number);
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        $db = Db::getInstance();
        $sql = $this->query();
        $sql->select('COUNT(*)');

        return (int)$db->getValue($sql);
    }

    /**
     * @return Collection
     */
    public function get()
    {
        $db = Db::getInstance();
        $sql = $this->query();
        $sql->select('*');

        foreach ($this->orderBy as $orderBy) {
            $sql->orderBy($orderBy);
        }
        if (null !== $this->limit) {
            $sql->limit($this->limit, $this->offset);
        }

        $rows = (array)$db->executeS($sql);

        return static::hydrate($rows);
    }

    /**
     * @return Model1
     */
    public function first()
    {
        $db = Db::getInstance();
        $sql = $this->query();
        $sql->select('*');

        foreach ($this->orderBy as $orderBy) {
            $sql->orderBy($orderBy);
        }

        $row = (array)$db->getRow($sql);

        return (new Model1())->hydrate($row);
    }

    /**
     * @return DbQuery
     */
    protected function query()
    {
        $sql = new DbQuery();
        $sql->from(Model1::$definition['table']);
        foreach ($this->where as $where) {
            $sql->where($where);
        }
        return $sql;
    }
}
