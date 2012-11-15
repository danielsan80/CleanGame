<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../app/config.yml"));

$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => __DIR__.'/../cache',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Guzzle\GuzzleServiceProvider());

$app->register(new Dan\CleanGame\Provider\ModelProvider());

$app['debug'] = true;

$app->get('/', function() use ($app) {
    
    $activities = $app['cleangame.manager.activity']->getActivities();
    return $app['twig']->render('activities.html.twig', array(
            'activities' => $activities,
        ));
    
})->bind('home');

return $app;