<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Dan\CleanGame\Model\Team;
use Symfony\Component\Validator\Constraints as Assert;

$app = new Silex\Application();

$app->register(new Igorw\Silex\ConfigServiceProvider(__DIR__."/../app/config/config.yml"));

$app->register(new Silex\Provider\SessionServiceProvider(), array(
    'session.storage.save_path' => __DIR__.'/../cache',
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

$app->register(new Silex\Provider\FormServiceProvider);
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.messages' => array(),
));

$app->register(new Guzzle\GuzzleServiceProvider());

$app->register(new Dan\CleanGame\Provider\ModelProvider());

$app['debug'] = true;

$app->get('/', function() use ($app) {
    
    return $app['twig']->render('home.html.twig', array(
        ));
    
})->bind('home');

$app->get('/home', function() use ($app) {
    
    return $app['twig']->render('home.html.twig', array(
        ));
    
})->bind('home');

$app->match('/teams', function() use ($app) {
    $request = $app['request'];
    
    $default = array(
        'number' => 3,
    );
    
    $form = $app['form.factory']->createBuilder('form', $default)
        ->add('name', 'text', array(
                'label' => ' ',
                'constraints' => array(new Assert\NotBlank(array('message' => 'Campo obbligatorio')))
            ))
        ->add('number', 'text', array(
                'label' => ' ',
                'constraints' => array(new Assert\NotBlank(array('message' => 'Campo obbligatorio')))
            ))
        ->getForm();

    if ('POST' == $request->getMethod()) {
        $form->bindRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            
            $team = new Team($data);
            $app['cleangame.manager.team']->save($team);
            $app->redirect('/teams');
        }
    }
    
    $teams = $app['cleangame.manager.team']->getTeams();
    
 
    return $app['twig']->render('teams.html.twig', array(
            'teams' => $teams,
            'form' => $form->createView(),
        ));
    
})->bind('teams');

$app->get('/activities', function() use ($app) {
    
    $activities = $app['cleangame.manager.activity']->getCurrentActivities();
    $teams = $app['cleangame.manager.team']->getTeams();
    
    return $app['twig']->render('activities.html.twig', array(
            'activities' => $activities,
            'teams' => $teams,
        ));
    
})->bind('activities');

$app->post('/activities/{id}/owner', function($id) use ($app) {
    
    $request = $app['request'];
    $owner = $request->get('owner');
    $activityManager = $app['cleangame.manager.activity'];
    $activity = $activityManager->find($id);
    $activity->setOwner($owner);
    $activityManager->save($activity);
    
    return true;
    
})->bind('setOwner');

$app->post('/activities/{id}/done', function($id) use ($app) {
    
    $request = $app['request'];
    $done = $request->get('done');
    $activityManager = $app['cleangame.manager.activity'];
    $activity = $activityManager->find($id);
    $activity->setDone($done);
    
    $activityManager->save($activity);
    
    return $done;
})->bind('setDone');

return $app;