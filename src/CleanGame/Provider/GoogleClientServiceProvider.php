<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleanGame\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Symfony HttpFoundation component Provider for sessions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class GoogleClientServiceProvider implements ServiceProviderInterface
{
    private $app;

    public function register(Application $app)
    {
        $client = new \CleanGame\Google\Client();
        $client->setApplicationName('Clean Game');
        // Visit https://code.google.com/apis/console?api=plus to generate your
        // client id, client secret, and to register your redirect uri.
        $client->setClientId('633253106061.apps.googleusercontent.com');
        $client->setClientSecret('dNp4bDPatHTbD-EZrSxPai2H');
        $client->setRedirectUri('http://cleangame.local.com/oauth2callback');
        $client->setDeveloperKey('AIzaSyB7200AAYs9sOjD7x9F-eF8oHtkLaMBPdI');
        $app['google.client'] = $client;
        
        $app['google.calendar'] = new \CleanGame\Google\CalendarService($app['google.client']);
    }
    
    public function boot(Application $app)
    {
        $app['google.client']->setSession($app['session']);
    }
}
