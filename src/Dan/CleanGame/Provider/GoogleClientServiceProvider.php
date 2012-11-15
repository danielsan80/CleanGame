<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dan\CleanGame\Provider;

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
        $client = new \Dan\CleanGame\Google\Client();
        $config = $app['config']['google']['client'];
        $client->setApplicationName($config['applicationName']);
        // Visit https://code.google.com/apis/console?api=plus to generate your
        // client id, client secret, and to register your redirect uri.
        $client->setClientId($config['clientId']);
        $client->setClientSecret($config['clientSecret']);
        $client->setRedirectUri($config['redirectUri']);
        $client->setDeveloperKey($config['developerKey']);
        $app['google.client'] = $client;
        
        $app['google.calendar'] = new \Dan\CleanGame\Google\CalendarService($app['google.client']);
    }
    
    public function boot(Application $app)
    {
        $app['google.client']->setSession($app['session']);
    }
}
