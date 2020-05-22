<?php


namespace MVC;


abstract class View
{
    abstract public function render(?array $content = null): ?string;
}