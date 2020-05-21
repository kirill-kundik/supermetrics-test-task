<?php


namespace Database\Drivers;

use \PDO;

final class PDOProxy
{
    /**
     * @var PDO
     */
    private $pdo = null;

    public function __construct($host, $user, $pass, $db, $port)
    {
        try {
            $this->pdo = \PDO(
                "mysql:host=" . $host . ";port=" . $port . ";dbname=" . $db,
                $user,
                $pass,
                array(\PDO::ATTR_PERSISTENT => true)
            );
        } catch (\PDOException $e) {
            trigger_error('Error: Could connect to a database ( ' . $e->getMessage() . '). \nCode : ' . $e->getCode());
            exit();
        }

        $this->pdo->exec("SET NAMES 'utf8'");
        $this->pdo->exec("SET CHARACTER SET utf8");
        $this->pdo->exec("SET CHARACTER_SET_CONNECTION=utf8");
        $this->pdo->exec("SET SQL_MODE = ''");
    }

    public function __destruct()
    {
        $this->pdo = null;
    }

    public function query($sql_query)
    {
        $statement = $this->pdo->prepare($sql_query);
        $result = false;

        try {
            if ($statement && $statement->execute()) {
                $data = array();

                while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                $result = new \stdClass();
                $result->row = (isset($data[0]) ? $data[0] : array());
                $result->rows = $data;
                $result->num_rows = $statement->rowCount();
            }
        } catch (\PDOException $e) {
            trigger_error('Error: ' . $e->getMessage() . ' \nCode : ' . $e->getCode() . ' \nSQL:' . $sql_query);
            exit();
        }

        if ($result) {
            return $result;
        } else {
            $result = new \stdClass();
            $result->row = array();
            $result->rows = array();
            $result->num_rows = 0;
            return $result;
        }
    }

    public function escape($value)
    {
        $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
        return str_replace($search, $replace, $value);
    }

    public function getLastId()
    {
        return $this->pdo->lastInsertId();
    }
}