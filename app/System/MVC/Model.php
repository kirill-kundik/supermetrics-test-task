<?php


namespace MVC;


use Database\DatabaseAdapter;
use Exception;

class Model
{
    protected DatabaseAdapter $conn;
    protected string $tablename;

    public final function __construct()
    {
        $this->conn = new DatabaseAdapter(
            DATABASE['Driver'],
            DATABASE['Host'],
            DATABASE['User'],
            DATABASE['Pass'],
            DATABASE['Name'],
            DATABASE['Port']
        );
    }

    private function escapeFunction($value)
    {
        return $this->conn->escape($value);
    }

    protected function update($values, $id = null)
    {
        $fields = array_map(array(&$this, "escapeFunction"), array_keys($values));
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

    protected function insert($values)
    {
        $fields = array_map(array(&$this, "escapeFunction"), array_keys($values));
        $values = array_map(array(&$this, "escapeFunction"), $values);

        $stmt = "INSERT INTO $this->tablename(" . join(", ", $fields) . ") VALUES (" . join(",", $values) . ")";
        $this->conn->query($stmt);
        return $this->conn->getLastId();
    }

    public function findAll()
    {
        return $this->conn->query("SELECT * FROM $this->tablename");
    }

    public function findById($id)
    {
        $res = $this->findBy(['id' => $id]);
        if (empty($res)) {
            throw new Exception("Record not found");
        }
        return $res[0];
    }

    public function findBy($values)
    {
        $fields = array_map(array(&$this, "escapeFunction"), array_keys($values));
        $values = array_map(array(&$this, "escapeFunction"), $values);

        $stmt = "SELECT * FROM $this->tablename";
        if (count($fields) > 0)
            $stmt .= " WHERE ";

        foreach ($fields as $key => $field) {
            $value = $values[$key];
            $stmt .= "$field = $value";

            if ($key < count($fields) - 1)
                $stmt .= " AND ";
        }

        return $this->conn->query($stmt);
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

    public function save($values)
    {
        if (array_key_exists("id", $values)) {
            $id = $values["id"];
            unset($values["id"]);
            $this->update($values, $id);
        } else {
            $this->insert($values);
        }
    }
}