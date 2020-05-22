<?php


namespace MVC;


use Exception;
use Http\Request;
use Http\Response;

class Controller
{
    public Request $request;
    public Response $response;

    protected ?string $modelName = null;
    protected ?string $viewName = null;

    protected ?Model $model = null;
    protected ?View $view = null;

    public function __construct()
    {
        $this->request = $GLOBALS['request'];
        $this->response = $GLOBALS['response'];

        try {
            if (isset($this->modelName))
                $this->model = loadResource('model', $this->modelName);
            else
                $this->model = null;
            if (isset($this->viewName))
                $this->view = loadResource('view', $this->viewName);
            else
                $this->view = null;
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }

    public function send($msg, $status = 200)
    {
        $this->response->setStatus($status);
        $this->response->setContent($msg);
    }
}