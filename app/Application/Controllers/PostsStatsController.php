<?php

use MVC\Controller;

class PostsStatsController extends Controller
{
    protected ?string $modelName = 'user';
    protected ?string $viewName = 'json';

    public function post()
    {
        $email = $this->request->post("email");
        $msg = $this->view->render(["email" => $email]);
        $this->send($msg);
    }

    public function test()
    {
        echo 'hello';
    }
}