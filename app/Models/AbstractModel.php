<?php
namespace App\Models;
use PDO;
use ReflectionException;

abstract class AbstractModel implements OrmModelInterface
{
    protected static PDO $db;
    protected static string $createdAtColumn = 'created_at';
    protected static string $updatedAtColumn = 'updated_at';

    public static function useConnection(PDO $conn) {
        static::$db = $conn;
    }

    public static function getConnection(): PDO
    {
        return static::$db;
    }

    public abstract function getId();
    /**
     * @throws ReflectionException
     */
    public static function morph(array $object) {
        $class = new \ReflectionClass(get_called_class()); // this is static method that's why i use get_called_class

        $entity = $class->newInstance();

        foreach($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            if (isset($object[$prop->getName()])) {
                $prop->setValue($entity,$object[$prop->getName()]);
            }
        }

        $entity->initialize(); // soft magic

        return $entity;
    }
    
    public function initialize()
    {
        
    }

    public static function getTableName()
    {
        return static::$tableName;
    }

    public function exists()
    {
        return $this->getId() > 0;
    }

    public function save() {

        $class = new \ReflectionClass($this);
        $tableName = static::getTableName();

        $props = [];

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();

            if ($propertyName === 'tableName' || $propertyName === 'id') {
                continue;
            }

            $props[$propertyName] = $this->{$propertyName};
        }


        if ($this->beforeSave($props) === false) {
            return false;
        }

        $setClause = implode(',', array_map(function ($key, $value) {
            return '`'.$key.'` = "'.$value.'"';
        }, array_keys($props), array_values($props)));

        if ($this->exists()) {
            $sqlQuery = 'UPDATE `'.$tableName.'` SET '.$setClause.' WHERE id = '.$this->getId();
        } else {
            $sqlQuery = 'INSERT INTO `'.$tableName.'` SET '.$setClause;
        }

        $result = static::$db->exec($sqlQuery);
        $errorCode = static::$db->errorCode();
        if ($errorCode) {
            if ($errorCode !== '00000') {
                throw new \Exception(implode('|', static::$db->errorInfo()), 500);
            }
        }


        if (!$this->exists()) {
            $lastId = static::$db->lastInsertId();
            $this->id = $lastId;
        }

        return $result;
    }

    /**
     * @throws ReflectionException|\Exception
     */
    public static function find($options = [], $limit = null) {

        $result = [];
        $query = 'SELECT * from ' . static::getTableName();

        $whereClause = '';
        $whereConditions = [];

        if (!empty($options)) {
            foreach ($options as $key => $value) {
                $value = is_string($value)
                    ? '"'. $value .'"'
                    : $value;

                $whereConditions[] = $key.' = '.$value;
            }
            $whereClause = ' WHERE '.implode(' AND ',$whereConditions);
        }

        $limitStr = $limit > 0 ? (' limit ' . $limit) : '';
        $stmt = static::$db->query($query . $whereClause . $limitStr);

        $errorCode = static::$db->errorCode();

        if ($errorCode) {
            if ($errorCode !== '00000') {
                throw new \Exception(implode('|', static::$db->errorInfo()), 500);
            }
        }

        $result = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $rawRow) {
            $result[] = static::morph($rawRow);
        }

        $stmt->closeCursor();

        if (count($result) > 0) {
            return $limit === 1
                ? $result[0]
                : $result;
        }

        return null;
    }

    public static function raw($query)
    {
        $stmt = static::$db->query($query);

        $errorCode = static::$db->errorCode();

        if ($errorCode) {
            if ($errorCode !== '00000') {
                throw new \Exception(implode('|', static::$db->errorInfo()), 500);
            }
        }

        $result = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $rawRow) {
            $result[] = $rawRow;
        }

        $stmt->closeCursor();

        return $result;
    }

    /**
     * Delete the record from the database.
     *
     * @access public
     */
    public function delete() {

        if (!$this->exists()) {
            throw new \Exception('Unable to delete object, record is new (and therefore doesn\'t exist in the database).', 400);
        }

        // build sql statement
        $sqlQuery = sprintf("DELETE FROM `%s` WHERE `id` = %s", static::getTableName(), $this->getId());

        $result = static::$db->exec($sqlQuery);

        $errorCode = static::$db->errorCode();
        if ($errorCode) {
            if ($errorCode !== '00000') {
                throw new \Exception(implode('|', static::$db->errorInfo()), 500);
            }
        }

        return $result;
    }

    public static function count() {
        $stmt = static::$db->query("SELECT count(*) FROM " . static::getTableName());

        $ret = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ret[] = $row;
        }

        $stmt->closeCursor();

        $count = $ret[0]['count(*)'];

        return max($count, 0);
    }

    protected function beforeSave(array &$data = []) {
        if (!$this->getId() && static::$createdAtColumn) {
            $data[static::$createdAtColumn] = date('Y-m-d H:i:s');
        }
        if (static::$updatedAtColumn) {
            $data[static::$updatedAtColumn] = date('Y-m-d H:i:s');
        }
    }

    protected function afterSave()
    {

    }
}