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
use Dan\CleanGame\Model\ActivityManager;
use Dan\CleanGame\Model\TeamManager;
use Dan\CleanGame\Model\Store;

/**
 * Symfony HttpFoundation component Provider for sessions.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ModelProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['cleangame.manager.activity'] = $app->share(function () use ($app) {
            $activityManager = new ActivityManager();
            $activityManager->setGuzzleClient($app['guzzle.client']);
            $activityManager->setGoogleConfig($app['config']['google']);
            $activityManager->setStore(new Store('/data','activities.yml','id'));

            return $activityManager;
        });
        
        $app['cleangame.manager.team'] = $app->share(function () use ($app) {
            $teamManager = new TeamManager();
            $teamManager->setActivityManager($app['cleangame.manager.activity']);
            $teamManager->setStore(new Store('/data','teams.yml','name'));

            return $teamManager;
        });
        
    }
    
    public function boot(Application $app)
    {
    }
}
