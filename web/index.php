<?php
require_once __DIR__ . '/../vendor/autoload.php';

use SixtyNine\WebSite\Controller\HomeController;
use Silex\Provider\FormServiceProvider;

define('ROOT', __DIR__ . '/../');

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app['home.controller'] = $app->share(function() use ($app) {
    return new HomeController($app);
});

$app->get('/', 'home.controller:indexAction');
$app->post('/', 'home.controller:indexAction');

$app->run();