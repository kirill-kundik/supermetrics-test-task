<?php


namespace MVC;


use Database\DatabaseAdapter;

abstract class Model
{
    private DatabaseAdapter $conn;
    protected $tablename;

    public final function __construct($conn)
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

    /**
     * @return mixed
     */
    abstract public function getTablename();
}