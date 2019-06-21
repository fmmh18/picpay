<?php

use Slim\App;

defined('DS') ?: define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') ?: define('ROOT', dirname(__DIR__) . DS);
defined('BASE') ?: define('BASE', "http://".$_SERVER['SERVER_NAME'] . DS."picpay/");

return function (App $app) {
    $container = $app->getContainer();
    // view renderer
    $container['view'] = function ($container)
    {
        $view = new \Slim\Views\Twig(ROOT.'App/view/',
            [
                'cache' => false
            ]);
        $view->getEnvironment()->addGlobal('baseURL',BASE);
        $view->addExtension(new \Slim\Views\TwigExtension(
            $container->router,
            $container->request->getUri()
        ));
        return $view;
    };
    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };
};
