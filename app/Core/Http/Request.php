<?php


namespace Http;


class Request
{
    public $request;

    public function __construct()
    {
        $this->request = $_REQUEST;
    }

    public function get(string $key = '')
    {
        if ($key != '')
            return isset($_GET[$key]) ? $this->clean($_GET[$key]) : null;

        return $this->clean($_GET);
    }

    public function post(string $key = '')
    {
        $_POST = json_decode(file_get_contents('php://input'), true);
        if ($key != '')
            return isset($_POST[$key]) ? $this->clean($_POST[$key]) : null;

        return $this->clean($_POST);
    }

    public function getMethod()
    {
        return strtoupper($this->getServerValue('REQUEST_METHOD'));
    }

    public function getClientIp()
    {
        return $this->getServerValue('REMOTE_ADDR');
    }

    public function getUrl()
    {
        return $this->getServerValue('QUERY_STRING');
    }

    private function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {

                // Delete key
                unset($data[$key]);

                // Set new clean key
                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }

    private function getServerValue(string $key = '')
    {
        return isset($_SERVER[strtoupper($key)])
            ? $this->clean($_SERVER[strtoupper($key)])
            : $this->clean($_SERVER);
    }
}