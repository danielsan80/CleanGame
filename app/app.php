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

$app->register(new CleanGame\Provider\GoogleClientServiceProvider());

$app->register(new Guzzle\GuzzleServiceProvider(), array(
//    'guzzle.services' => '/path/to/services.js',
//    'guzzle.class_path' => '/path/to/guzzle/src'
));

$app['debug'] = true;

$app->get('/', function() use ($app) {
    
    $cli = $app['guzzle.client'];
    $cli->setBaseUrl('https://www.googleapis.com/calendar/v3?key='.$app['config']['google']['client']['developerKey']);
    $request = $cli->get('calendars/'.$app['config']['google']['calendar']['id'].'/events');
    $query = $request->getQuery();
    $start = new \DateTime();
    $end = new \DateTime('+2 week');
    $query->set('timeMin', $start->format('Y-m-d\TH:i:s.000P'));
    $query->set('timeMax', $end->format('Y-m-d\TH:i:s.000P'));
    $query->set('orderBy', 'startTime');
    $query->set('singleEvents', 'true');
    $response = $request->send();
    $caltas = json_decode($response->getBody(true));
    var_dump($caltas->items);
    
    if ($app['google.client']->getAccessToken()) {
        $calser = $app['google.calendar'];
        $calList = $calser->calendarList->listCalendarList();
        $items = $calser->events->listEvents($app['config']['google']['calendar']['id']);
        var_dump($items);
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