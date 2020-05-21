<?php


namespace MVC;


use Database\DatabaseAdapter;

abstract class Model
{
    private DatabaseAdapter $conn;

    protected string $tablename;

    public final function __construct()
    {
        if (!isset($this->tablename))
            trigger_error(get_class($this) . ' must have a $tablename');

        $this->conn = new DatabaseAdapter(
            DATABASE['Driver'],
            DATABASE['Host'],
            DATABASE['User'],
            DATABASE['Pass'],
            DATABASE['Name'],
            DATABASE['Port']
        );
    }

    public function findAll()
    {
        return $this->conn->query("SELECT * FROM $this->tablename");
    }

    public function findById($id)
    {
        $id = $this->conn->escape($id);
        $res = $this->conn->query("SELECT * FROM $this->tablename WHERE id = $id");
        if (empty($res)) {
            throw new \Exception("Record not found");
        }
        return $res[0];
    }

    public function update($fields, $values, $id = null)
    {
        assert(count($fields) == count($values), 'Fields and Values must be same size');
        $fields = array_map(array(&$this, "escapeFunction"), $fields);
        $values = array_map(array(&$this, "escapeFunction"), $values);

        $stmt = "UPDATE $this->tablename SET ";
        foreach ($fields as $key => $field) {
            $value = $values[$key];
            $stmt .= "$field = $value";

            if ($key < count($fields) - 1)
                $stmt .= ", ";
        }
        if (!is_null($id)) {
            $id = $this->conn->escape($id);
            $stmt .= " WHERE id=$id";
        }
        $this->conn->query($stmt);
    }

    public function delete($id = null)
    {
        $stmt = "DELETE FROM $this->tablename";
        if (!is_null($id)) {
            $id = $this->conn->escape($id);
            $stmt .= " WHERE id=$id";
        }
        $this->conn->query($stmt);
    }

    public function insert($fields, $values)
    {
        assert(count($fields) == count($values), 'Fields and Values must be same size');
        $fields = array_map(array(&$this, "escapeFunction"), $fields);
        $values = array_map(array(&$this, "escapeFunction"), $values);

        $stmt = "INSERT INTO $this->tablename(" . join(", ", $fields) . ") VALUES (" . join(",", $values) . ")";
        $this->conn->query($stmt);
        return $this->conn->getLastId();
    }

    private function escapeFunction($value)
    {
        return $this->conn->escape($value);
    }
}