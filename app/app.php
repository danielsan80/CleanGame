<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = true;

$app->get('/', function(){
    return new Response('<h1>hello world</h1>', 200);
    
})->bind('home');

return $app;