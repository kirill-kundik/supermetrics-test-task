<?php


namespace Views;


use MVC\View;

class JsonView extends View
{
    public function render($content)
    {
        return json_encode($content);
    }
}