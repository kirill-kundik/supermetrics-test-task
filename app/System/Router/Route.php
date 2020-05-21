<?php


namespace Router;


use Exception;

class Route
{
    private string $method;
    private string $pattern;
    private $callback;

    private array $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTION'];

    public function __construct(string $method, string $pattern, $callback)
    {
        $this->method = $this->validateMethod(strtoupper($method));
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    private function validateMethod(string $method)
    {
        if (in_array(strtoupper($method), $this->methods))
            return $method;

        throw new Exception('Invalid Method Name');
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getCallback()
    {
        return $this->callback;
    }
}