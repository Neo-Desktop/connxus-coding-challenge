<?php
/**
 * Created by PhpStorm.
 * User: Amrit
 * Date: 2017-10-05
 * Time: 4:23 PM
 */

namespace App\Models;

use PDO;
use PDOStatement;

abstract class BaseModel implements \ArrayAccess
{
    /**
     * @var PDO
     */
    public static $connection;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes array, working copy
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Original array, reflects data in storage
     *
     * @var array
     */
    private $original = [];

    /**
     * Maps column [key] to output [value] in toArray();
     *
     * @var array
     */
    protected $columnMap = [];

    /**
     * A stack of "wheres"
     *
     * @var array
     */
    private $wheres = [];

    /**
     * Saves an object to the DB
     *
     * @return bool
     */
    public function save()
    {
        // separate columns and data
        $rowDiff = array_merge($this->original, $this->attributes);
        $keys = array_keys($rowDiff);

        // format the keys for SQL
        $columns = implode(',', $keys);

        // extract the number of placeholders necessary
        $valuePlaceholders = implode(',', array_fill(0, count($keys), '?'));
        $updatePlaceholders = array_map(function ($key) {
            return $key . '=?';
        }, $keys);
        $updatePlaceholders[] = $this->primaryKey . '=LAST_INSERT_ID('
            . $this->primaryKey . ')';
        $updatePlaceholders = implode(',', $updatePlaceholders);

        // generate two copies of values
        $values = array_merge(array_values($rowDiff), array_values($rowDiff));

        // create the prepared statement
        $sql = 'INSERT INTO %s (%s) VALUES (%s) ON DUPLICATE KEY UPDATE %s';
        $sql = sprintf($sql, $this->table, $columns, $valuePlaceholders,
            $updatePlaceholders);

        // run against db
        $statement = self::$connection->prepare($sql);
        if (!$statement) {
            var_dump(self::$connection->errorInfo());
        }
        $result = $statement->execute($values);

        // update object state
        $this->getByIndex(self::$connection->lastInsertId());

        return $result;
    }

    /**
     * Conditional where routine
     *
     * @param $column
     * @param $value
     *
     * @return $this
     */
    public function where($column, $value)
    {
        $this->wheres[$column] = $value;
        return $this;
    }

    /**
     * Helper for get() and find()
     * Generates and executes the sql stack
     *
     * @return PDOStatement
     */
    private function baseGetFind()
    {
        $baseSql = 'SELECT * FROM %s';
        $sprintfValues = [$this->table];
        $sqlValues = [];

        // perform object filtering for wheres
        if (count($this->wheres) > 0) {
            $baseSql .= ' WHERE %s';
            $sprintfValues[] = '1=1';

            var_dump($this->wheres);

            // first where
            foreach ($this->wheres as $column => $value) {
                $baseSql .= ' %s';
                $sqlValues[] = $value;
                $sprintfValues[] = 'AND ' . $column . '=?';
            }
        }

        $sql = vsprintf($baseSql, $sprintfValues);

        $statement = self::$connection->prepare($sql);
        if (!$statement) {
            echo self::$connection->errorInfo();
        }
        $statement->execute($sqlValues);

        return $statement;
    }

    /**
     * Executes a sql statement with an expectation for a single row
     *
     * @return $this
     */
    public function find()
    {
        $statement = $this->baseGetFind();

        // setup output arrays
        $this->original = $this->fetchAssociativeSingle($statement);
        $this->attributes = $this->original;

        return $this;
    }

    /**
     * Executes a sql statement with an expectation for multiple rows
     *
     * @return $this
     */
    public function get()
    {
        $statement = $this->baseGetFind();

        // setup output arrays
        $this->original = $this->fetchAssociativeAll($statement);
        $this->attributes = $this->original;

        return $this;
    }

    /**
     * Deletes a row in the DB
     */
    public function delete()
    {
        $baseSql = 'DELETE FROM %s';
        $sprintfValues = [$this->table];
        $sqlValues = [];

        $this->wheres[$this->primaryKey] = $this{$this->primaryKey};

        // perform object filtering for wheres
        if (count($this->wheres) > 0) {
            $baseSql .= ' WHERE %s';
            foreach ($this->wheres as $column => $value) {
                $sqlValues[] = $value;
                $sprintfValues[] = $column . '=?';
            }
        }

        $sql = vsprintf($baseSql, $sprintfValues);
        $statement = self::$connection->prepare($sql);
        if (!$statement) {
            echo self::$connection->errorInfo();
        }

        $statement->execute($sqlValues);

        return $statement;
    }

    /**
     * Fetches a table row by ID
     *
     * @param $index
     *
     * @return self
     */
    public function getByIndex($index)
    {
        $sql = 'SELECT * FROM %s WHERE %s = :id';
        $sql = sprintf($sql, $this->table, $this->primaryKey);

        $statement = self::$connection->prepare($sql);
        $values = ['id' => $index];

        if (!$statement) {
            echo self::$connection->errorInfo();
        }
        $statement->execute($values);

        $this->original = $this->fetchAssociativeSingle($statement);
        $this->attributes = $this->original;

        return $this;
    }

    /**
     * Perform a fetch on a PDO Statement
     *
     * @param PDOStatement $statement
     *
     * @return array
     *
     */
    public function fetchAssociativeSingle(PDOStatement $statement)
    {
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Perform a fetchAll on a PDO Statement
     *
     * @param PDOStatement $statement
     *
     * @return array
     *
     */
    public function fetchAssociativeAll(PDOStatement $statement)
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * The object as represented in array fashion
     * Applies the column mapping for display purposes
     *
     * @return array
     */
    public function toArray()
    {
        if (empty($this->original)) {
            return $this->original;
        }

        $out = $this->original;

        if (!is_array($this->original[0])) {
            $out = [$out];
        }

        // apply the column map here
        $out = array_map(function ($row) {
            $mapOut = [];
            foreach ($row as $key => $value) {
                if (in_array($key, array_keys($this->columnMap))) {
                    $key = $this->columnMap[$key];
                }
                $mapOut[$key] = $value;
            }
            return $mapOut;
        }, $out);

        if (!is_array($this->original[0])) {
            $out = $out[0];
        }
        return $out;
    }

    /***** Magic Getters / Setters *****/
    /**
     * Grabs data from the attributes array
     *
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * Modifies the attributes array
     *
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Needed for empty() to work correctly
     *
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        echo $name . '</br>';
        return false === empty($this->attributes[$name]);
    }

    /***** Array Access Methods *****/
    /**
     * Needed for \ArrayAccess
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return true === $this->{$offset};
    }

    /**
     * Needed for \ArrayAccess
     *
     * @param mixed $offset
     *
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * Needed for \ArrayAccess
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * Needed for \ArrayAccess
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }
}
