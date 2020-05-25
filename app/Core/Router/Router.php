<?php


namespace Router;


use Exception;

class Router
{
    private array $routes = [];
    private array $matchRoutes = [];
    private $url;
    private string $method;
    private array $params = [];
    private $response;

    public function __construct(string $url, string $method)
    {
        $this->url = rtrim($url, '/');
        $this->method = $method;

        $this->response = $GLOBALS['response'];
    }

    public function get($pattern, $callback)
    {
        $this->addRoute("GET", $pattern, $callback);
    }

    public function post($pattern, $callback)
    {
        $this->addRoute('POST', $pattern, $callback);
    }

    public function put($pattern, $callback)
    {
        $this->addRoute('PUT', $pattern, $callback);
    }

    public function delete($pattern, $callback)
    {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    public function addRoute($method, $pattern, $callback)
    {
        array_push($this->routes, new Route($method, $pattern, $callback));
    }

    private function getMatchRoutersByRequestMethod()
    {
        foreach ($this->routes as $value) {
            if (strtoupper($this->method) == $value->getMethod())
                array_push($this->matchRoutes, $value);
        }
    }

    private function getMatchRoutersByPattern($pattern)
    {
        $this->matchRoutes = [];
        foreach ($pattern as $value) {
            if ($this->dispatch($this->url, $value->getPattern()))
                array_push($this->matchRoutes, $value);
        }
    }

    public function dispatch($url, $pattern)
    {
        preg_match_all('@:([\w]+)@', $pattern, $params, PREG_PATTERN_ORDER);

        $patternAsRegex = preg_replace_callback('@:([\w]+)@', [$this, 'convertPatternToRegex'], $pattern);

        if (substr($pattern, -1) === '/') {
            $patternAsRegex = $patternAsRegex . '?';
        }
        $patternAsRegex = '@^' . $patternAsRegex . '(&amp;)*@';

        // check match request url
        if (preg_match($patternAsRegex, $url, $paramsValue)) {
            array_shift($paramsValue);
            foreach ($params[0] as $key => $value) {
                $val = substr($value, 1);
                if ($paramsValue[$val]) {
                    $this->setParams($val, urlencode($paramsValue[$val]));
                }
            }

            return true;
        }

        return false;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    private function setParams($key, $value)
    {
        $this->params[$key] = $value;
    }

    private function convertPatternToRegex($matches)
    {
        $key = str_replace(':', '', $matches[0]);
        return '(?P<' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+)';
    }

    public function run()
    {
        if (!is_array($this->routes) || empty($this->routes))
            throw new Exception('NON-Object Route Set');

        $this->getMatchRoutersByRequestMethod();
        $this->getMatchRoutersByPattern($this->matchRoutes);

        if (!$this->matchRoutes || empty($this->matchRoutes)) {
            $this->sendNotFound();
        } else {
            // call to callback method
            if (is_callable($this->matchRoutes[0]->getCallback()))
                call_user_func($this->matchRoutes[0]->getCallback(), $this->params);
            else
                $this->runController($this->matchRoutes[0]->getCallback(), $this->params);
        }
    }

    private function runController($controller, $params)
    {
        $parts = explode('@', $controller);
        $file = CONTROLLERS . ucfirst($parts[0]) . 'Controller.php';

        if (file_exists($file)) {
            require_once($file);

            // controller class
            $controller = ucfirst($parts[0]) . 'Controller';

            if (class_exists($controller))
                $controller = new $controller();
            else
                $this->sendNotFound();

            // set function in controller
            if (isset($parts[1])) {
                $method = $parts[1];

                if (!method_exists($controller, $method))
                    $this->sendNotFound();

            } else {
                $method = 'index';
            }

            // call to controller
            if (is_callable([$controller, $method]))
                call_user_func([$controller, $method], $params);
            else
                $this->sendNotFound();
        }
    }

    private function sendNotFound()
    {
        $this->response->setStatus(404);
        $this->response->setContent(json_encode(['error' => 'Route not found', 'status_code' => 404]));
    }
}