<?php

$router->get('/', function () {
    echo 'Welcome ';
});

$router->get('/home', 'home@index');

