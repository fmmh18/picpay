<?php

use App\controller\Principal;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    $container['Principal'] = function($container) use ($app)
    {
        return new app\controller\Principal($container);
    };

    $app->get('/',Principal::class.":index");

};
