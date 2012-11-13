<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => __DIR__.'/../cache',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new CleanGame\Provider\GoogleClientServiceProvider());

$app['debug'] = true;

$app->get('/', function() use ($app) {
    
    if ($app['google.client']->getAccessToken()) {
        $calList = $app['google.calendar']->calendarList->listCalendarList();
        $activities = array(
            array('title' => 'to clean the wc'),
            array('title' => 'to sweep'),
            array('title' => 'to clean the glass'),
        );
        return $app['twig']->render('activities.html.twig', array(
            'activities' => $activities,
        ));
    } else {
        return $app['twig']->render('login.html.twig', array(
            'url' => $app['google.client']->createAuthUrl(),
        ));
    }
    
   
    
    
    
})->bind('home');

$app->get('/logout', function() use ($app) {
    $app['google.client']->logout();
    return $app->redirect('/');
})->bind('logout');

$app->get('/oauth2callback', function() use ($app) {
    if ($code = $app['request']->get('code')) {
        $app['google.client']->authenticate($code);
    }
    return $app->redirect('/');
})->bind('oauth_return');

return $app;