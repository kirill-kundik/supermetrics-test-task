<?php


namespace Database;


class DatabaseAdapter
{
    private $conn;

    public function __construct($driver, $host, $user, $pass, $db, $port)
    {
        $class = '\Database\Drivers\\' . $driver;

        if (class_exists($class)) {
            $this->conn = new $class($host, $user, $pass, $db, $port);
        } else {
            exit('Error: Could not load database driver ' . $driver);
        }
    }

    public function query($sql_query)
    {
        return $this->conn->query($sql_query);
    }

    public function escape($value)
    {
        return $this->conn->escape($value);
    }

    public function getLastId()
    {
        return $this->conn->getLastId();
    }
}