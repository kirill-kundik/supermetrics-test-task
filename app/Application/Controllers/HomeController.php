<?php

use MVC\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $this->send("hello again");
    }
}