<?php

$router->post('/api/v1/stats', 'postsStats@post');

$router->get('/api/v1/statsTest', 'postsStats@test');