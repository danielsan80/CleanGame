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

$app->post('/activity/{id}/owner', function($id) use ($app) {
    
    $request = $app['request'];
    $owner = $request->get('owner');
    $activityManager = $app['cleangame.manager.activity'];
    $activity = $activityManager->find($id);
    $activity->setOwner($owner);
    $activityManager->save($activity);
    
    return true;
    
})->bind('setOwner');

$app->post('/activity/{id}/done', function($id) use ($app) {
    
    $request = $app['request'];
    $done = $request->get('done');
    $activityManager = $app['cleangame.manager.activity'];
    $activity = $activityManager->find($id);
    $activity->setDone($done);
    
    $activityManager->save($activity);
    
    return $done;
})->bind('setDone');

return $app;