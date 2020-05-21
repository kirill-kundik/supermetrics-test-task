<?php


namespace MVC;


use Exception;

abstract class Controller
{
    public $request;
    public $response;

    protected $modelName = null;
    protected $viewName = null;

    private $model = null;
    private $view = null;

    public function __construct()
    {
        $this->request = $GLOBALS['request'];
        $this->response = $GLOBALS['response'];

        try {
            if (isset($this->modelName))
                $this->model = $this->loadResource(MODELS, $this->modelName);
            else
                $this->model = null;
            if (isset($this->viewName))
                $this->view = $this->loadResource(VIEWS, $this->viewName);
            else
                $this->view = null;
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }

    private function loadResource($resource, $resourceName)
    {
        $file = $resource . ucfirst($resourceName) . '.php';

        if (file_exists($file)) {
            require_once $file;

            $model = 'Models' . str_replace('/', '', ucwords($resourceName, '/'));
            // check class exists
            if (class_exists($model))
                return new $model;
            else
                throw new Exception(sprintf('{ %s } this model class not found', $resourceName));
        } else {
            throw new Exception(sprintf('{ %s } this model file not found', $file));
        }
    }

    public function send($msg, $status = 200)
    {
        $this->response->setStatus($status);
        $this->response->setContent($msg);
    }
}