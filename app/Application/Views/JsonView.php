<?php

use MVC\View;

class JsonView extends View
{
    public function render(?array $content = null): ?string
    {
        if (!is_null($content))
            return json_encode($content);
        else
            null;
    }
}