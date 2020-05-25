<?php

require SERVICES . "UserService.php";
require SERVICES . "SupermetricsApiService.php";
require SERVICES . "PostsStatisticsService.php";

use MVC\Controller;

class PostsStatsController extends Controller
{
    protected ?string $modelName = 'user';
    protected ?string $viewName = 'json';

    public function post()
    {
        $email = $this->request->post("email");
        $name = $this->request->post("name");
        $clientId = $this->request->post("client_id");

        $userService = new UserService(
            $this->model, ["email" => $email, "name" => $name, "client_id" => $clientId]
        );

        $supermetricsApiService = new SupermetricsApiService($userService);
        $posts = $supermetricsApiService->fetchPosts();

        $msg = $this->view->render($posts);
        $this->send($msg);
    }

    public function test()
    {
        echo 'hello';
    }
}