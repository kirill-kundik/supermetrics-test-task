<?php


namespace MVC;


use Exception;

abstract class Controller
{
    public $request;
    public $response;

    public function __construct()
    {
        $this->request = $GLOBALS['request'];
        $this->response = $GLOBALS['response'];
    }

    /**
     * get Model
     *
     * @param string $model
     *
     * @return object
     * @throws Exception
     */
    public function model($model)
    {
        $file = MODELS . ucfirst($model) . '.php';

        if (file_exists($file)) {
            require_once $file;

            $model = 'Models' . str_replace('/', '', ucwords($model, '/'));
            // check class exists
            if (class_exists($model))
                return new $model;
            else
                throw new Exception(sprintf('{ %s } this model class not found', $model));
        } else {
            throw new Exception(sprintf('{ %s } this model file not found', $file));
        }
    }

    public function send($msg, $status = 200)
    {
        $this->response->setStatusCode($status);
        $this->response->setContent($msg);
    }
}