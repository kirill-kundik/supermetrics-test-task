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

        $stats = $this->processUserStatsRequest($email, $name, $clientId);

        $msg = $this->view->render($stats);
        $this->send($msg);
    }

    public function test()
    {
        $stats = $this->processUserStatsRequest(
            "your@email.address",
            "Your Name",
            "ju16a6m81mhid5ue1z3v2g0uh"
        );

        $msg = $this->view->render($stats);
        $this->send($msg);
    }

    private function processUserStatsRequest($email, $name, $clientId)
    {
        $userService = new UserService(
            $this->model, ["email" => $email, "name" => $name, "client_id" => $clientId]
        );

        $supermetricsApiService = new SupermetricsApiService($userService);
        $posts = $supermetricsApiService->fetchPosts();
        return (new PostsStatisticsService($posts))->getStats();
    }
}