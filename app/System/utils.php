<?php

function loadResource($resource, $resourceName)
{
    $resource = ucfirst($resource);

    switch ($resource) {
        case 'View':
            $resourceDir = VIEWS;
            break;
        case 'Model':
            $resourceDir = MODELS;
            break;
        case 'Controller':
            $resourceDir = CONTROLLERS;
            break;
        default:
            throw new Exception(sprintf('{ %s } this resources type does not exist', $resource));
    }

    $file = $resourceDir . ucfirst($resourceName) . $resource . '.php';

    if (file_exists($file)) {
        require_once $file;

        $resourceClass = str_replace('/', '', ucwords($resourceName, '/')) . $resource;
        // check class exists
        if (class_exists($resourceClass))
            return new $resourceClass;
        else
            throw new Exception(sprintf('{ %s } this resource class not found', $resourceName));
    } else {
        throw new Exception(sprintf('{ %s } this resource file not found', $file));
    }
}