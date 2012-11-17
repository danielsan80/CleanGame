<?php
error_reporting(E_ALL);

require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../app/app.php';

$app->run();
