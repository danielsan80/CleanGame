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

$app['debug'] = true;

$app->get('/', function() use ($app) {
    require_once __DIR__.'/../src/Google/google-api-php-client/src/Google_Client.php';
    require_once __DIR__.'/../src/Google/google-api-php-client/src/contrib/Google_CalendarService.php';
    
    $client = new Google_Client();
    $client->setApplicationName('Clean Game');
    // Visit https://code.google.com/apis/console?api=plus to generate your
    // client id, client secret, and to register your redirect uri.
    $client->setClientId('633253106061.apps.googleusercontent.com');
    $client->setClientSecret('dNp4bDPatHTbD-EZrSxPai2H');
    $client->setRedirectUri('http://cleangame.local.com');
    $client->setDeveloperKey('AIzaSyB7200AAYs9sOjD7x9F-eF8oHtkLaMBPdI');
    $cal = new Google_CalendarService($client);
    if ($app['request']->get('logout')) {
        $app['session']->unset('token');
    }

    if ($code = $app['request']->get('code')) {
        $client->authenticate($code);
        $app['session']->set('token', $client->getAccessToken());
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
    }

    if ($app['session']->get('token')) {
        $client->setAccessToken($app['session']->get('token'));
    }

    if ($client->getAccessToken()) {
        $calList = $cal->calendarList->listCalendarList();
        print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";
        $app['session']->set('token', $client->getAccessToken());
    } else {
        $authUrl = $client->createAuthUrl();
        print "<a class='login' href='$authUrl'>Connect Me!</a>";
    }
    
    $activities = array(
        array('title' => 'to clean the wc'),
        array('title' => 'to sweep'),
        array('title' => 'to clean the glass'),
    );
    
    return $app['twig']->render('activities.html.twig', array(
        'activities' => $activities,
    ));
    
})->bind('home');

$app->get('/oauth2callback', function() use ($app) {
    return $app->redirect('/');
})->bind('oauth_return');

return $app;